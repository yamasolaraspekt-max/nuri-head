<?php
 declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CustomerHistoryImportController extends Controller
{
    private const REQUIRED_COLS = [
        'id','customer_id','alternative_id','product_id','phase_id','activity_id','section_id',
        'done_by','marked_by','is_done',
        'done_reason','plan_time','is_time','d_time',
        'done_date','notes','has_document','done_history','old_stage',
        'created_at','updated_at',
    ];

    public function create()
    {
        return view('admin.imports.customer_histories');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sql_file'   => ['nullable','file','mimes:sql,txt','max:20480'],
            'sql_text'   => ['nullable','string'],
            'dry_run'    => ['sometimes','boolean'],
            'upsert'     => ['sometimes','boolean'],
            'batch_size' => ['sometimes','integer','min:100','max:5000'],
        ]);

        // Load SQL
        $sql = '';
        if ($request->file('sql_file')) {
            $sql = file_get_contents($request->file('sql_file')->getRealPath()) ?: '';
        } elseif (!empty($data['sql_text'])) {
            $sql = (string) $data['sql_text'];
        }
        if (trim($sql) === '') {
            return back()->withErrors(['sql_text' => 'Upload a .sql file or paste SQL text.']);
        }

        // Clean
        $sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql); // BOM
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql); // block comments
        $sql = preg_replace('/^\s*(--|#).*$\n?/m', '', $sql); // line comments

        // Parse all INSERTS
        $blocks = $this->extractAllInsertsForTable($sql, 'customer_histories');
        if (empty($blocks)) {
            return back()->withErrors(['sql_text' => 'No INSERTs found for table customer_histories.']);
        }

        $rows = [];
        foreach ($blocks as [$cols, $tuples]) {
            foreach ($tuples as $tuple) {
                $vals = $this->splitValues($tuple);
                if (count($vals) !== count($cols)) continue;

                $row = [];
                foreach ($cols as $i => $col) {
                    $row[$col] = $this->sqlLiteralToPhp($vals[$i]);
                }

                // Add missing new cols
                foreach (['done_reason','plan_time','is_time','d_time'] as $extra) {
                    if (!array_key_exists($extra, $row)) $row[$extra] = null;
                }

                // Normalize JSON fields
                $row['has_document'] = $this->normalizeHasDocument($row['has_document'] ?? null);
                $row['done_history'] = $this->normalizeDoneHistory($row['done_history'] ?? null);

                // Keep only valid cols
                $row = $this->keepAllowedColumns($row);
                $rows[] = $row;
            }
        }

        if (empty($rows)) {
            return back()->withErrors(['sql_text' => 'Parsed 0 valid rows after normalization.']);
        }

        $dryRun    = $request->boolean('dry_run');
        $upsert    = $request->boolean('upsert');
        $batchSize = (int) ($request->input('batch_size', 1000));

        $inserted = 0; $failed = 0;

        if (!$dryRun) {
            DB::transaction(function () use ($rows, $upsert, $batchSize, &$inserted, &$failed) {
                $columns    = array_keys($rows[0]);
                $updateCols = array_values(array_diff($columns, ['id']));
                foreach (array_chunk($rows, $batchSize) as $batch) {
                    try {
                        if ($upsert) {
                            DB::table('customer_histories')->upsert($batch, ['id'], $updateCols);
                        } else {
                            DB::table('customer_histories')->insert($batch);
                        }
                        $inserted += count($batch);
                    } catch (\Throwable $e) {
                        // salvage row by row
                        foreach ($batch as $row) {
                            try {
                                if ($upsert) {
                                    DB::table('customer_histories')->upsert([$row], ['id'], $updateCols);
                                } else {
                                    DB::table('customer_histories')->insert($row);
                                }
                                $inserted++;
                            } catch (\Throwable $ee) {
                                $failed++;
                            }
                        }
                    }
                }
            });
        }

        return back()->with('ok', sprintf(
            "Done. DryRun=%s, Upsert=%s, Parsed=%d, Inserted=%d, Failed=%d",
            $dryRun ? 'yes' : 'no', $upsert ? 'yes' : 'no', count($rows), $inserted, $failed
        ))->with('sample', array_slice($rows, 0, 3));
    }

    // --- Helpers ---

    private function keepAllowedColumns(array $row): array
    {
        $out = [];
        foreach (self::REQUIRED_COLS as $c) {
            $out[$c] = $row[$c] ?? null;
        }
        return $out;
    }

    private function extractAllInsertsForTable(string $sql, string $table): array
    {
        $pattern = '/INSERT\s+INTO\s+`?' . preg_quote($table,'/') . '`?\s*\((.*?)\)\s*VALUES\s*(.*?);/is';
        preg_match_all($pattern, $sql, $matches, PREG_SET_ORDER);
        $blocks = [];
        foreach ($matches as $m) {
            $cols = array_map(fn($c) => trim($c, " `"), explode(',', $m[1]));
            $tuples = $this->splitTuples($m[2]);
            $blocks[] = [$cols, $tuples];
        }
        return $blocks;
    }

    private function splitTuples(string $s): array
    {
        $out=[]; $cur=''; $depth=0; $inStr=false; $esc=false;
        for ($i=0;$i<strlen($s);$i++) {
            $ch=$s[$i];
            if ($inStr) {
                $cur.=$ch;
                if ($esc) { $esc=false; }
                elseif ($ch==='\\') $esc=true;
                elseif ($ch==="'") $inStr=false;
            } else {
                if ($ch==="'") { $inStr=true; $cur.=$ch; }
                elseif ($ch==='(') { if($depth++>0)$cur.=$ch; }
                elseif ($ch===')') {
                    if(--$depth>0)$cur.=$ch;
                    else { $out[]=trim($cur); $cur=''; }
                } else { if($depth>0)$cur.=$ch; }
            }
        }
        return $out;
    }

    private function splitValues(string $tuple): array
    {
        $vals=[]; $cur=''; $inStr=false; $esc=false;
        for ($i=0;$i<strlen($tuple);$i++) {
            $ch=$tuple[$i];
            if ($inStr) {
                $cur.=$ch;
                if ($esc) $esc=false;
                elseif ($ch==='\\') $esc=true;
                elseif ($ch==="'") $inStr=false;
            } else {
                if ($ch==="'") { $inStr=true; $cur.=$ch; }
                elseif ($ch===',') { $vals[]=trim($cur); $cur=''; }
                else $cur.=$ch;
            }
        }
        if (trim($cur)!=='') $vals[]=trim($cur);
        return $vals;
    }

    private function sqlLiteralToPhp(string $lit)
    {
        $lit=trim($lit);
        if (strcasecmp($lit,'NULL')===0) return null;
        if (preg_match("/^'.*'$/s",$lit)) {
            $inner=substr($lit,1,-1);
            $inner=str_replace(["\\n","\\r","\\t","\\0"],["\n","\r","\t","\0"],$inner);
            $inner=str_replace(["\\\\","\\'","\\\""],["\\","'","\""],$inner);
            return $inner;
        }
        return $lit;
    }

    private function normalizeHasDocument($val)
    {
        if (!$val) return null;
        $decoded=json_decode($val,true);
        if (json_last_error()===0) return json_encode($decoded);
        return json_encode([$val]);
    }

    private function normalizeDoneHistory($val)
    {
        if (!$val) return null;
        $try=json_decode($val,true);
        if (json_last_error()===0 && $try!==null) {
            if (is_string($try)) {
                $try2=json_decode($try,true);
                if (json_last_error()===0) return json_encode($try2);
            }
            return json_encode($try);
        }
        $clean=trim($val,"\"'");
        $clean=str_replace(['\\"','\\\\'],['"','\\'],$clean);
        $try=json_decode($clean,true);
        return json_last_error()===0 ? json_encode($try) : null;
    }
}
