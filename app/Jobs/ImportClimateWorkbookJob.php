<?php

namespace App\Jobs;

use App\Models\ClimateEvaluationRow;
use App\Models\ClimateLocation;
use App\Models\ClimateMonthlyData;
use App\Models\ClimateSolarMonthlyData;
use App\Models\ClimateStation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;
use XMLReader;
use ZipArchive;

class ImportClimateWorkbookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
     
    public int $timeout = 3600;
    public int $tries = 1;

    public function __construct(
        public string $storedPath,
        public ?string $originalName = null
    ) {
    }

    public function handle(): void
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $absolutePath = Storage::disk('local')->path($this->storedPath);

        if (! file_exists($absolutePath)) {
            throw new \RuntimeException('Stored import file was not found: ' . $this->storedPath);
        }

        $sheets = $this->readXlsxSheets($absolutePath, [
            'List.Station.TA',
            'Tab.StationMapping',
            'Data.TA.HD',
            'Data.Sol',
            'Calc.Evaluation',
            'Tab.Evaluation',
        ]);

        $stationRows    = $sheets['List.Station.TA'] ?? [];
        $mappingRows    = $sheets['Tab.StationMapping'] ?? [];
        $taHdRows       = $sheets['Data.TA.HD'] ?? [];
        $solRows        = $sheets['Data.Sol'] ?? [];
        $evaluationRows = $sheets['Calc.Evaluation'] ?? $sheets['Tab.Evaluation'] ?? [];

        if (empty($stationRows) || empty($mappingRows) || empty($taHdRows) || empty($solRows)) {
            throw new \RuntimeException('Required sheets are missing in the uploaded workbook.');
        }

        DB::transaction(function () use ($stationRows, $mappingRows, $taHdRows, $solRows, $evaluationRows) {
            $stationPayloads = $this->collectStations($stationRows, $taHdRows, $solRows, $evaluationRows);
            $postcodeMap     = $this->collectPostcodeMappings($mappingRows);

            foreach ($stationPayloads as $stationId => $payload) {
                $mappedPostcodes = $postcodeMap[$stationId] ?? [];

                ClimateStation::updateOrCreate(
                    ['station_id' => (string) $stationId],
                    [
                        'name'             => $payload['name'] ?: ('Station ' . $stationId),
                        'region'           => $payload['region'],
                        'country_code'     => $payload['country_code'],
                        'lat'              => $payload['lat'],
                        'lon'              => $payload['lon'],
                        'elevation'        => $payload['elevation'],
                        'mapped_postcodes' => array_values(array_unique($mappedPostcodes)),
                    ]
                );
            }

            $this->importLocations($mappingRows);

            $stationIdMap = ClimateStation::pluck('id', 'station_id')->toArray();

            // Fallback imports from source sheets
            $this->importLegacyMonthlyFromSourceSheets($taHdRows, $stationIdMap);
            $this->importLegacySolarFromSourceSheets($solRows, $stationIdMap);

            // Replace evaluation snapshot on each run
            ClimateEvaluationRow::query()->delete();

            if (! empty($evaluationRows)) {
                $this->importEvaluationRows($evaluationRows);
                $this->importDetailedMonthlyData($evaluationRows, $stationIdMap);
                $this->importDetailedSolarData($evaluationRows, $stationIdMap);
            }
        });

        Storage::disk('local')->delete($this->storedPath);
    }

    public function failed(Throwable $e): void
    {
        Log::error('Climate import job failed', [
            'file'          => $this->storedPath,
            'original_name' => $this->originalName,
            'error'         => $e->getMessage(),
        ]);
    }

    protected function collectStations(array $stationRows, array $taHdRows, array $solRows, array $evaluationRows = []): array
    {
        $stations = [];

        foreach ($stationRows as $row) {
            $stationId = $this->normalizeStationId($row['ID_Station'] ?? null);

            if (! $stationId) {
                continue;
            }

            $stations[$stationId] = [
                'name'         => $this->cleanString($row['Name_Station'] ?? null),
                'region'       => $this->cleanString($row['Name_Region_Station'] ?? null),
                'country_code' => null,
                'lat'          => $this->toDecimal($row['Latitude'] ?? null),
                'lon'          => $this->toDecimal($row['Longitude'] ?? null),
                'elevation'    => $this->toFloat($row['Altitude'] ?? null),
            ];
        }

        foreach ([$taHdRows, $solRows] as $rows) {
            foreach ($rows as $row) {
                $stationId = $this->normalizeStationId($row['ID_Station'] ?? null);

                if (! $stationId) {
                    continue;
                }

                if (! isset($stations[$stationId])) {
                    $stations[$stationId] = [
                        'name'         => $this->cleanString($row['Name_Station'] ?? null),
                        'region'       => $this->cleanString($row['Name_Region_Station'] ?? null),
                        'country_code' => null,
                        'lat'          => $this->toDecimal($row['Latitude'] ?? null),
                        'lon'          => $this->toDecimal($row['Longitude'] ?? null),
                        'elevation'    => $this->toFloat($row['Altitude'] ?? null),
                    ];
                }
            }
        }

        foreach ($evaluationRows as $row) {
            $stationId = $this->extractEvaluationStationId($row);

            if (! $stationId) {
                continue;
            }

            if (! isset($stations[$stationId])) {
                $stations[$stationId] = [
                    'name'         => $this->cleanString($row['Name_Station'] ?? null),
                    'region'       => $this->cleanString($row['Name_Region_Station'] ?? null),
                    'country_code' => $this->cleanString($row['Code_Country_Input'] ?? null),
                    'lat'          => $this->toDecimal($row['Latitude'] ?? null),
                    'lon'          => $this->toDecimal($row['Longitude'] ?? null),
                    'elevation'    => $this->toFloat($row['Altitude'] ?? null),
                ];
            }
        }

        return $stations;
    }

    protected function collectPostcodeMappings(array $mappingRows): array
    {
        $map = [];

        foreach ($mappingRows as $row) {
            $postcode = $this->cleanString($row['ID_Location'] ?? null);

            if (! $postcode) {
                continue;
            }

            foreach (['ID_Station_01', 'ID_Station_02', 'ID_Station_03'] as $stationColumn) {
                $stationId = $this->normalizeStationId($row[$stationColumn] ?? null);

                if (! $stationId) {
                    continue;
                }

                if (! isset($map[$stationId])) {
                    $map[$stationId] = [];
                }

                $map[$stationId][] = $postcode;
            }
        }

        foreach ($map as $stationId => $postcodes) {
            $map[$stationId] = array_values(array_unique($postcodes));
        }

        return $map;
    }

    protected function importLocations(array $mappingRows): void
    {
        foreach ($mappingRows as $row) {
            $postcode = $this->cleanString($row['ID_Location'] ?? null);

            if (! $postcode) {
                continue;
            }

            ClimateLocation::updateOrCreate(
                [
                    'postcode'     => $postcode,
                    'country_code' => $this->cleanString($row['Code_Country'] ?? null),
                ],
                [
                    'location_mapping_id'         => $this->cleanString($row['ID_Location_StationMapping'] ?? null),
                    'location_type'               => $this->cleanString($row['Type_LocationID'] ?? null),
                    'mapping_version'             => $this->toInt($row['Index_MappingVersion'] ?? null),
                    'mapping_version_name'        => $this->cleanString($row['Name_MappingVersion'] ?? null),
                    'mapping_version_description' => $this->cleanString($row['Description_MappingVersion'] ?? null),
                    'name'                        => $this->cleanString($row['Name_Location'] ?? null),
                    'lat'                         => $this->toDecimal($row['Latitude'] ?? null),
                    'lon'                         => $this->toDecimal($row['Longitude'] ?? null),
                    'elevation'                   => $this->toFloat($row['Altitude'] ?? null),
                    'station_01_id'               => $this->normalizeStationId($row['ID_Station_01'] ?? null),
                    'station_02_id'               => $this->normalizeStationId($row['ID_Station_02'] ?? null),
                    'station_03_id'               => $this->normalizeStationId($row['ID_Station_03'] ?? null),
                    'distance_station_01'         => $this->toFloat($row['Distance_Station_01'] ?? null),
                    'distance_station_02'         => $this->toFloat($row['Distance_Station_02'] ?? null),
                    'distance_station_03'         => $this->toFloat($row['Distance_Station_03'] ?? null),
                ]
            );
        }
    }

    protected function importEvaluationRows(array $evaluationRows): void
    {
        foreach ($evaluationRows as $row) {
            if (! $this->isValidEvaluationRow($row)) {
                continue;
            }

            $evaluationId = $this->cleanString($row['ID_Evaluation'] ?? null);
            $quantityCode = $this->cleanString($row['Code_Quantity'] ?? null);

            $ltaValues = [];
            for ($m = 1; $m <= 12; $m++) {
                $col = 'M_LTA_' . str_pad((string) $m, 2, '0', STR_PAD_LEFT);
                $ltaValues[$m] = $this->toFloat($row[$col] ?? null);
            }

            $periodValues = [];
            foreach ($row as $key => $value) {
                if (preg_match('/^M_(\d{4})_(\d{2})$/', $key, $matches)) {
                    $periodValues[$matches[1] . '-' . $matches[2]] = $this->toFloat($value);
                }
            }

            ClimateEvaluationRow::create([
                'evaluation_id'               => $evaluationId,
                'evaluation_name'             => $this->cleanString($row['Name_Evaluation'] ?? null),
                'postcode'                    => $this->cleanString($row['ID_Postcode_Input'] ?? null),
                'country_code'                => $this->cleanString($row['Code_Country_Input'] ?? null),
                'location_name'               => $this->cleanString($row['Name_Location'] ?? null),
                'location_station_mapping_id' => $this->cleanString($row['ID_Location_StationMapping'] ?? null),
                'station_id'                  => $this->extractEvaluationStationId($row),
                'quantity_code'               => $quantityCode,
                'data_type_code'              => $this->cleanString($row['Code_DataType'] ?? null),
                'orientation_code'            => $this->cleanString($row['Code_Orientation'] ?? null),
                'orientation_name'            => $this->cleanString($row['Name_Orientation'] ?? null),
                'orientation_degree'          => $this->toFloat($row['Degreee_Orientation'] ?? null),
                'inclination_degree'          => $this->toFloat($row['Degree_Inclination'] ?? null),
                'lta_values'                  => $ltaValues,
                'period_values'               => $periodValues,
                'meta'                        => $row,
            ]);
        }
    }

    protected function importLegacyMonthlyFromSourceSheets(array $taHdRows, array $stationIdMap): void
    {
        $monthNames = $this->monthNames();

        foreach ($taHdRows as $row) {
            $stationId = $this->normalizeStationId($row['ID_Station'] ?? null);
            $code      = $this->cleanString($row['Code_Quantity'] ?? null);

            if (! $stationId || ! isset($stationIdMap[$stationId]) || ! $code) {
                continue;
            }

            $field = match ($code) {
                'TA'    => 'avg_temp',
                'HD_15' => 'heating_days',
                'CD_22' => 'cooling_days',
                default => null,
            };

            if (! $field) {
                continue;
            }

            for ($month = 1; $month <= 12; $month++) {
                $column = 'M_LTA_' . str_pad((string) $month, 2, '0', STR_PAD_LEFT);
                $value  = $this->toFloat($row[$column] ?? null);

                if ($value === null) {
                    continue;
                }

                $record = ClimateMonthlyData::firstOrCreate(
                    [
                        'climate_station_id' => $stationIdMap[$stationId],
                        'dataset_scope'      => 'lta',
                        'dataset_label'      => 'source-lta',
                        'year'               => null,
                        'month_num'          => $month,
                    ],
                    [
                        'month' => $monthNames[$month],
                    ]
                );

                $record->{$field} = $value;
                $record->save();
            }
        }
    }

    protected function importLegacySolarFromSourceSheets(array $solRows, array $stationIdMap): void
    {
        $monthNames = $this->monthNames();

        foreach ($solRows as $row) {
            $stationId = $this->normalizeStationId($row['ID_Station'] ?? null);
            $code      = $this->cleanString($row['Code_Quantity'] ?? null);

            if (! $stationId || ! isset($stationIdMap[$stationId])) {
                continue;
            }

            if ($code !== 'I_Hor') {
                continue;
            }

            for ($month = 1; $month <= 12; $month++) {
                $column = 'M_LTA_' . str_pad((string) $month, 2, '0', STR_PAD_LEFT);
                $value  = $this->toFloat($row[$column] ?? null);

                if ($value === null) {
                    continue;
                }

                ClimateSolarMonthlyData::updateOrCreate(
                    [
                        'climate_station_id' => $stationIdMap[$stationId],
                        'dataset_scope'      => 'lta',
                        'dataset_label'      => 'source-lta',
                        'year'               => null,
                        'month_num'          => $month,
                        'surface_type'       => 'horizontal',
                        'tilt_angle'         => null,
                        'orientation'        => 'Hor',
                        'row_kind'           => 'month',
                    ],
                    [
                        'month'        => $monthNames[$month],
                        'value_kwh_m2' => $value,
                    ]
                );
            }
        }
    }

    protected function importDetailedMonthlyData(array $evaluationRows, array $stationIdMap): void
    {
        $monthNames = $this->monthNames();

        foreach ($evaluationRows as $row) {
            if (! $this->isValidEvaluationRow($row)) {
                continue;
            }

            $quantityCode = $this->cleanString($row['Code_Quantity'] ?? null);
            if (! $quantityCode) {
                continue;
            }

            $stationId = $this->extractEvaluationStationId($row);

            if (! $stationId || ! isset($stationIdMap[$stationId])) {
                continue;
            }

            $climateStationId = $stationIdMap[$stationId];

            $field = match ($quantityCode) {
                'GTZ_20_15', 'GTZ' => 'degree_days',
                'HD_15', 'HD'      => 'heating_days',
                'CD_22', 'CD'      => 'cooling_days',
                'TA'               => 'avg_temp',
                'TA_HD'            => 'avg_temp_heating_days',
                default            => null,
            };

            if (! $field) {
                continue;
            }

            for ($month = 1; $month <= 12; $month++) {
                $col   = 'M_LTA_' . str_pad((string) $month, 2, '0', STR_PAD_LEFT);
                $value = $this->toFloat($row[$col] ?? null);

                if ($value === null) {
                    continue;
                }

                $record = ClimateMonthlyData::firstOrCreate(
                    [
                        'climate_station_id' => $climateStationId,
                        'dataset_scope'      => 'lta',
                        'dataset_label'      => '2004-2023',
                        'year'               => null,
                        'month_num'          => $month,
                    ],
                    [
                        'month' => $monthNames[$month],
                    ]
                );

                $record->{$field} = $value;
                $record->save();
            }

            foreach ($row as $key => $rawValue) {
                if (! preg_match('/^M_(\d{4})_(\d{2})$/', $key, $m)) {
                    continue;
                }

                $year  = (int) $m[1];
                $month = (int) $m[2];
                $value = $this->toFloat($rawValue);

                if ($value === null) {
                    continue;
                }

                $record = ClimateMonthlyData::firstOrCreate(
                    [
                        'climate_station_id' => $climateStationId,
                        'dataset_scope'      => 'period',
                        'dataset_label'      => '2024/2025',
                        'year'               => $year,
                        'month_num'          => $month,
                    ],
                    [
                        'month' => $monthNames[$month],
                    ]
                );

                $record->{$field} = $value;
                $record->save();
            }
        }
    }

    protected function importDetailedSolarData(array $evaluationRows, array $stationIdMap): void
    {
        $monthNames = $this->monthNames();

        foreach ($evaluationRows as $row) {
            if (! $this->isValidEvaluationRow($row)) {
                continue;
            }

            $quantityCode = $this->cleanString($row['Code_Quantity'] ?? null);
            if (! $quantityCode) {
                continue;
            }

            $stationId = $this->extractEvaluationStationId($row);

            if (! $stationId || ! isset($stationIdMap[$stationId])) {
                continue;
            }

            $climateStationId = $stationIdMap[$stationId];
            $orientation      = $this->cleanString($row['Code_Orientation'] ?? null) ?: $this->cleanString($row['Name_Orientation'] ?? null);
            $inclination      = $this->toFloat($row['Degree_Inclination'] ?? null);

            $surfaceType = match (true) {
                $quantityCode === 'I_Hor' => 'horizontal',
                $inclination === 90.0     => 'vertical',
                $inclination === 45.0     => 'tilted',
                default                   => null,
            };

            if (! $surfaceType) {
                continue;
            }

            $finalOrientation = $orientation ?: ($surfaceType === 'horizontal' ? 'Hor' : null);

            for ($month = 1; $month <= 12; $month++) {
                $col   = 'M_LTA_' . str_pad((string) $month, 2, '0', STR_PAD_LEFT);
                $value = $this->toFloat($row[$col] ?? null);

                if ($value === null) {
                    continue;
                }

                ClimateSolarMonthlyData::updateOrCreate(
                    [
                        'climate_station_id' => $climateStationId,
                        'dataset_scope'      => 'lta',
                        'dataset_label'      => '2004-2023',
                        'year'               => null,
                        'month_num'          => $month,
                        'surface_type'       => $surfaceType,
                        'tilt_angle'         => $surfaceType === 'tilted' ? $inclination : null,
                        'orientation'        => $finalOrientation,
                        'row_kind'           => 'month',
                    ],
                    [
                        'month'        => $monthNames[$month],
                        'value_kwh_m2' => $value,
                    ]
                );
            }

            foreach ($row as $key => $rawValue) {
                if (! preg_match('/^M_(\d{4})_(\d{2})$/', $key, $m)) {
                    continue;
                }

                $year  = (int) $m[1];
                $month = (int) $m[2];
                $value = $this->toFloat($rawValue);

                if ($value === null) {
                    continue;
                }

                ClimateSolarMonthlyData::updateOrCreate(
                    [
                        'climate_station_id' => $climateStationId,
                        'dataset_scope'      => 'period',
                        'dataset_label'      => '2024/2025',
                        'year'               => $year,
                        'month_num'          => $month,
                        'surface_type'       => $surfaceType,
                        'tilt_angle'         => $surfaceType === 'tilted' ? $inclination : null,
                        'orientation'        => $finalOrientation,
                        'row_kind'           => 'month',
                    ],
                    [
                        'month'        => $monthNames[$month],
                        'value_kwh_m2' => $value,
                    ]
                );
            }
        }
    }

    protected function readXlsxSheets(string $filePath, array $requiredSheetNames): array
    {
        $zip = new ZipArchive();

        if ($zip->open($filePath) !== true) {
            throw new \RuntimeException('Could not open uploaded .xlsx file.');
        }

        try {
            $sharedStrings = $this->extractSharedStrings($zip);
            $sheetPaths    = $this->extractSheetPaths($zip);
            $result        = [];

            foreach ($requiredSheetNames as $sheetName) {
                if (! isset($sheetPaths[$sheetName])) {
                    continue;
                }

                $worksheetXml = $zip->getFromName($sheetPaths[$sheetName]);

                if ($worksheetXml === false) {
                    continue;
                }

                $result[$sheetName] = $this->extractSheetRows($worksheetXml, $sharedStrings);
            }

            return $result;
        } finally {
            $zip->close();
        }
    }

    protected function extractSharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');

        if ($xml === false) {
            return [];
        }

        $sxe = simplexml_load_string($xml);

        if (! $sxe) {
            return [];
        }

        $strings = [];

        foreach ($sxe->si as $si) {
            $textParts = $si->xpath('.//*[local-name()="t"]');
            $value     = '';

            if ($textParts) {
                foreach ($textParts as $part) {
                    $value .= (string) $part;
                }
            }

            $strings[] = $value;
        }

        return $strings;
    }

    protected function extractSheetPaths(ZipArchive $zip): array
    {
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        $relsXml     = $zip->getFromName('xl/_rels/workbook.xml.rels');

        if ($workbookXml === false || $relsXml === false) {
            throw new \RuntimeException('Workbook metadata is missing.');
        }

        $workbook = simplexml_load_string($workbookXml);
        $rels     = simplexml_load_string($relsXml);

        if (! $workbook || ! $rels) {
            throw new \RuntimeException('Workbook metadata could not be parsed.');
        }

        $workbook->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $rels->registerXPathNamespace('rel', 'http://schemas.openxmlformats.org/package/2006/relationships');

        $relMap = [];
        foreach ($rels->xpath('//rel:Relationship') as $rel) {
            $id     = (string) $rel['Id'];
            $target = (string) $rel['Target'];

            if ($id === '' || $target === '') {
                continue;
            }

            $target = ltrim($target, '/');
            if (! str_starts_with($target, 'xl/')) {
                $target = 'xl/' . $target;
            }

            $relMap[$id] = $target;
        }

        $sheetMap = [];
        foreach ($workbook->xpath('//x:sheets/x:sheet') as $sheet) {
            $name = (string) $sheet['name'];

            $attributes = $sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
            $relId      = (string) ($attributes['id'] ?? '');

            if ($name !== '' && $relId !== '' && isset($relMap[$relId])) {
                $sheetMap[$name] = $relMap[$relId];
            }
        }

        return $sheetMap;
    }

    protected function extractSheetRows(string $worksheetXml, array $sharedStrings): array
    {
        $reader = new XMLReader();
        $reader->XML($worksheetXml);

        $headers = [];
        $rows    = [];

        while ($reader->read()) {
            if ($reader->nodeType !== XMLReader::ELEMENT || $reader->localName !== 'row') {
                continue;
            }

            $rowXml  = $reader->readOuterXML();
            $rowNode = simplexml_load_string($rowXml);

            if (! $rowNode) {
                continue;
            }

            $sparseValues = [];

            foreach ($rowNode->c as $cell) {
                $ref  = (string) ($cell['r'] ?? '');
                $type = (string) ($cell['t'] ?? '');

                if ($ref === '') {
                    continue;
                }

                $columnLetters = preg_replace('/\d+/', '', $ref);
                $colIndex      = $this->columnLettersToIndex($columnLetters);
                $value         = null;

                if ($type === 's') {
                    $sharedIndex = (int) ($cell->v ?? 0);
                    $value = $sharedStrings[$sharedIndex] ?? null;
                } elseif ($type === 'inlineStr') {
                    $parts  = $cell->xpath('.//*[local-name()="t"]');
                    $inline = '';

                    if ($parts) {
                        foreach ($parts as $part) {
                            $inline .= (string) $part;
                        }
                    }

                    $value = $inline;
                } else {
                    $value = isset($cell->v) ? (string) $cell->v : null;
                }

                $sparseValues[$colIndex] = $value;
            }

            if (empty($sparseValues)) {
                continue;
            }

            ksort($sparseValues);
            $maxIndex = max(array_keys($sparseValues));
            $fullRow  = array_fill(0, $maxIndex + 1, null);

            foreach ($sparseValues as $index => $value) {
                $fullRow[$index] = $value;
            }

            if (empty($headers)) {
                $headers = array_map(function ($h) {
                    return trim((string) $h);
                }, $fullRow);
                continue;
            }

            $hasAnyValue = false;
            foreach ($fullRow as $value) {
                if ($value !== null && $value !== '') {
                    $hasAnyValue = true;
                    break;
                }
            }

            if (! $hasAnyValue) {
                continue;
            }

            $assoc = [];
            foreach ($headers as $i => $header) {
                if ($header === '') {
                    continue;
                }

                $assoc[$header] = $fullRow[$i] ?? null;
            }

            $rows[] = $assoc;
        }

        $reader->close();

        return $rows;
    }

    protected function isValidEvaluationRow(array $row): bool
    {
        $evaluationId = $this->cleanString($row['ID_Evaluation'] ?? null);
        $quantityCode = $this->cleanString($row['Code_Quantity'] ?? null);
        $rowType      = $this->cleanString($row['Code_Evaluation_RowType'] ?? null);

        if (! $evaluationId && ! $quantityCode) {
            return false;
        }

        // Skip total helper rows that have no useful quantity
        if (! $quantityCode) {
            return false;
        }

        // Keep common real rows; if rowType is empty but quantity exists, keep it
        if ($rowType && in_array($rowType, ['Header', 'Comment', 'Info'], true)) {
            return false;
        }

        return true;
    }

    protected function extractEvaluationStationId(array $row): ?string
    {
        return $this->normalizeStationId(
            $row['ID_Station']
                ?? $row['ID_Station_Predefined']
                ?? $row['ID_Station_Calculate']
                ?? $row['ID_Station_Manual']
                ?? null
        );
    }

    protected function monthNames(): array
    {
        return [
            1  => 'Jan',
            2  => 'Feb',
            3  => 'Mar',
            4  => 'Apr',
            5  => 'May',
            6  => 'Jun',
            7  => 'Jul',
            8  => 'Aug',
            9  => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];
    }

    protected function columnLettersToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $length  = strlen($letters);
        $index   = 0;

        for ($i = 0; $i < $length; $i++) {
            $index = ($index * 26) + (ord($letters[$i]) - 64);
        }

        return $index - 1;
    }

    protected function normalizeStationId($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function cleanString($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function toDecimal($value): ?float
    {
        return $this->toFloat($value);
    }

    protected function toFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);
        $value = str_replace(',', '.', $value);

        if ($value === '' || ! is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    protected function toInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) round((float) $value);
    }
}