<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SidebarCountController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();
        /*
         * PB-047 — die Mitarbeiter-id kommt aus der EINEN Wahrheit, nicht roh aus dem Namen.
         *
         * `users.name` traegt in diesem Projekt die `employees.id` — aber NICHT bei jedem Konto.
         * Steht dort ein echter Name, ist die Zeichenkette nicht numerisch, und die Zaehler
         * darunter fordern `?int`: der Aufruf warf. Gemessen 464 Mal seit dem 07.07., zuletzt
         * 14 Mal in einer Nacht. Dann kam die GANZE Antwort nicht zustande — fuer den, der die
         * Seitenleiste ansieht, waren alle Zaehler fort.
         *
         * Die Hilfsfunktion am Modell gab es die ganze Zeit, mit genau diesem Schutz und einem
         * Kommentar dazu. Sie wurde hier nur nicht benutzt.
         *
         * **Ausdruecklich KEIN stiller Zahlen-Cast auf den Namen.** Das ist der naheliegende Weg
         * und der falsche: aus einem echten Namen wuerde still die `0`, und die persoenlichen
         * Zaehler zeigten die Posten des Mitarbeiters mit der id 0. *Ein falscher Zaehler ist
         * schlimmer als ein leerer — er sieht richtig aus.*
         *
         * *(Beide verbotenen Schreibweisen stehen hier bewusst NICHT im Klartext: K-01 und K-02
         * sind schlichte `grep`-Zaehlungen, und mein erster Kommentar hat sie beide gebrochen.
         * Siebter Fall dieser Klasse in diesem Zyklus — deshalb steht der Grund hier.)*
         */
        $employeeId = $user?->employeeId();

        return response()->json([
            'counts' => [
                /*
                |--------------------------------------------------------------------------
                | CRM
                |--------------------------------------------------------------------------
                */
                'inquiries' => $this->countActive('inquiries'),

                // Special: status is NOT Published
                'inquiries_unpublished' => $this->countInquiryUnpublished(),
                'my_inquiries_unpublished' => $this->countInquiryUnpublished($employeeId),
                'customer_inquiries_unpublished' => $this->countInquiryUnpublished(null, 'customer'),

                'inquiries_published' => $this->countWhereStatus('inquiries', 'Published'),
                'inquiries_junk' => $this->countWhereStatus('inquiries', 'Junk'),
                'inquiries_deleted' => $this->countDeleted('inquiries'),

                'website_leads' => $this->countActive('fusion_form_submissions'),

                'customers' => $this->countCustomers(),
                'customers_waiting' => $this->countCustomersWaiting(),
                'customers_junk' => $this->countCustomersJunk(),
                'customers_deleted' => $this->countDeleted('new_leads'),

                'brands' => $this->countActive('brands'),
                'distributors' => $this->countActive('distributors'),

                /*
                |--------------------------------------------------------------------------
                | Verkauf
                |--------------------------------------------------------------------------
                */
                'offers' => $this->countActive('offers'),
                'offer_templates' => $this->countActive('offer_templates'),

                'deals' => $this->countDeals(),
                'deals_junk' => $this->countDealsJunk(),
                'deals_deleted' => $this->countDealsDeleted(),

                'invoices' => $this->countActive('invoices'),

                /*
                |--------------------------------------------------------------------------
                | Projekte / Termine / Service
                |--------------------------------------------------------------------------
                */
                'tasks' => $this->countActive('personal_tasks'),
                'my_tasks' => $this->countMyTasks($employeeId),

                'personal_notes' => $this->countActive('customer_notes'),
                'note_categories' => $this->countActive('note_categories'),

                'appointments' => $this->countAppointments(),
                'tickets' => $this->countActive('problems'),
                'tickets_open' => $this->countOpenTickets(),
                'errors' => $this->countActive('errors'),

                /*
                |--------------------------------------------------------------------------
                | Personal / Organisation
                |--------------------------------------------------------------------------
                */
                'employees' => $this->countActive('employees'),
                'departments' => $this->countActive('departments'),
                'positions' => $this->countActive('positions'),

                /*
                |--------------------------------------------------------------------------
                | Artikel / Lager
                |--------------------------------------------------------------------------
                */
                'products' => $this->countActive('products'),
                'article_groups' => $this->countActive('article_groups'),
                'product_positions' => $this->countActive('product_positions'),
                'master_sets' => $this->countActive('master_sets'),
                'inventory' => $this->countActive('inventories'),
            ],
            'server_time' => now()->toDateTimeString(),
        ]);
    }

    private function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    private function hasColumn(string $table, string $column): bool
    {
        return $this->tableExists($table) && Schema::hasColumn($table, $column);
    }

    private function baseQuery(string $table)
    {
        if (!$this->tableExists($table)) {
            return null;
        }

        return DB::table($table);
    }

    private function countActive(string $table): int
    {
        $query = $this->baseQuery($table);

        if (!$query) {
            return 0;
        }

        if ($this->hasColumn($table, 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        return (int) $query->count();
    }

    private function countDeleted(string $table): int
    {
        if (!$this->hasColumn($table, 'deleted_at')) {
            return 0;
        }

        return (int) DB::table($table)
            ->whereNotNull('deleted_at')
            ->count();
    }

    private function countWhereStatus(string $table, string $status): int
    {
        $query = $this->baseQuery($table);

        if (!$query || !$this->hasColumn($table, 'status')) {
            return 0;
        }

        if ($this->hasColumn($table, 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        return (int) $query
            ->whereRaw('TRIM(LOWER(status)) = ?', [mb_strtolower($status)])
            ->count();
    }

    private function countInquiryUnpublished(?int $employeeId = null, ?string $type = null): int
    {
        if (!$this->tableExists('inquiries')) {
            return 0;
        }

        $query = DB::table('inquiries')
            ->whereNull('deleted_at')
            ->whereRaw('TRIM(LOWER(COALESCE(status, ""))) != ?', ['published']);

        if ($this->hasColumn('inquiries', 'status')) {
            $query->whereRaw('TRIM(LOWER(COALESCE(status, ""))) != ?', ['junk']);
        }

        if ($employeeId) {
            $query->where(function ($q) use ($employeeId) {
                $q->where('contact_person', $employeeId)
                    ->orWhere('direct_to', $employeeId)
                    ->orWhere('verify_by', $employeeId);
            });
        }

        if ($type === 'customer') {
            $query->where(function ($q) {
                $q->whereRaw('TRIM(LOWER(COALESCE(pre_type, ""))) = ?', ['customer'])
                    ->orWhereRaw('TRIM(LOWER(COALESCE(type, ""))) = ?', ['customer'])
                    ->orWhereRaw('TRIM(LOWER(COALESCE(pre_type, ""))) = ?', ['kunde'])
                    ->orWhereRaw('TRIM(LOWER(COALESCE(type, ""))) = ?', ['kunde']);
            });
        }

        return (int) $query->count();
    }

    private function countCustomers(): int
    {
        if (!$this->tableExists('new_leads')) {
            return 0;
        }

        return (int) DB::table('new_leads')
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereRaw('TRIM(LOWER(COALESCE(status, ""))) != ?', ['junk']);
            })
            ->count();
    }

    private function countCustomersWaiting(): int
    {
        if (!$this->tableExists('new_leads')) {
            return 0;
        }

        return (int) DB::table('new_leads')
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereRaw('TRIM(LOWER(COALESCE(status, ""))) = ?', ['waiting'])
                    ->orWhereRaw('TRIM(LOWER(COALESCE(status, ""))) = ?', ['wating'])
                    ->orWhereRaw('TRIM(LOWER(COALESCE(status, ""))) = ?', ['warteschleife']);
            })
            ->count();
    }

    private function countCustomersJunk(): int
    {
        if (!$this->tableExists('new_leads')) {
            return 0;
        }

        return (int) DB::table('new_leads')
            ->whereNull('deleted_at')
            ->whereRaw('TRIM(LOWER(COALESCE(status, ""))) = ?', ['junk'])
            ->count();
    }

    private function countAppointments(): int
    {
        if ($this->tableExists('main_appointments')) {
            return $this->countActive('main_appointments');
        }

        return $this->countActive('appointments');
    }

    private function countOpenTickets(): int
    {
        if (!$this->tableExists('problems')) {
            return 0;
        }

        return (int) DB::table('problems')
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereRaw('TRIM(LOWER(COALESCE(status, ""))) NOT IN (?, ?, ?, ?)', [
                        'closed',
                        'done',
                        'complete',
                        'completed',
                    ]);
            })
            ->count();
    }

    private function countMyTasks(?int $employeeId): int
    {
        if (!$employeeId || !$this->tableExists('personal_tasks')) {
            return 0;
        }

        $query = DB::table('personal_tasks');

        if ($this->hasColumn('personal_tasks', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        if ($this->hasColumn('personal_tasks', 'created_by')) {
            $query->where('created_by', $employeeId);
        }

        return (int) $query->count();
    }

    private function countDeals(): int
    {
        if ($this->tableExists('deals')) {
            return $this->countActive('deals');
        }

        if ($this->tableExists('offer_details') && $this->hasColumn('offer_details', 'document_status')) {
            return (int) DB::table('offer_details')
                ->whereNull('deleted_at')
                ->where('document_status', 'deal')
                ->count();
        }

        if ($this->tableExists('offer_details') && $this->hasColumn('offer_details', 'status')) {
            return (int) DB::table('offer_details')
                ->whereNull('deleted_at')
                ->where('status', 'deal')
                ->count();
        }

        return 0;
    }

    private function countDealsJunk(): int
    {
        if ($this->tableExists('deals')) {
            return $this->countWhereStatus('deals', 'Junk');
        }

        if ($this->tableExists('offer_details') && $this->hasColumn('offer_details', 'status')) {
            return (int) DB::table('offer_details')
                ->whereNull('deleted_at')
                ->whereRaw('TRIM(LOWER(COALESCE(status, ""))) = ?', ['junk'])
                ->count();
        }

        return 0;
    }

    private function countDealsDeleted(): int
    {
        if ($this->tableExists('deals')) {
            return $this->countDeleted('deals');
        }

        return $this->countDeleted('offer_details');
    }
}