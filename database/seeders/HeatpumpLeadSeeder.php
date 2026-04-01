<?php

namespace Database\Seeders;

use App\Models\NewLeads;
use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;
use App\Models\ArticleGroup;
use App\Models\ProductPosition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HeatpumpLeadSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Find the Wärmepumpe article group
        $heatpumpGroup = ArticleGroup::query()
            ->where('article_group', 'LIKE', '%Wärmepumpe%')
            ->orWhere('article_group', 'LIKE', '%Wärmepumpen%')
            ->orWhere('article_group', 'LIKE', '%WP%')
            ->first();

        if (! $heatpumpGroup) {
            throw new \RuntimeException('No ArticleGroup for heat pumps (Wärmepumpe) found.');
        }

        // 2) Use the first ProductPosition as template for department/service + suggested employees
        $productPosition = ProductPosition::query()
            ->where('article_group_id', $heatpumpGroup->id)
            ->first();

        $defaultDepartmentId    = $productPosition?->department_id;
        $defaultServiceId       = $productPosition?->service_id;
        $defaultEmployeeId      = null;
        $defaultFieldEmployeeId = null;

        if ($productPosition && $productPosition->position_ids) {
            $ids = json_decode($productPosition->position_ids, true) ?: [];
            if (is_array($ids) && count($ids)) {
                $defaultEmployeeId      = $ids[0] ?? null;
                $defaultFieldEmployeeId = $ids[1] ?? $defaultEmployeeId;
            }
        }

        $entries = [
        [
            'name_raw' => 'Kohlhaas, Arne',
            'address'  => 'Bahnhofstraße 26, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-12 + SMO S40 + EME20 + AXC40 + VST 11* + FtX40* + AXC40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 152769408',
                'inbetriebnahme' => '2019-06-24',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 18-0346002-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Breu, Helmut',
            'address'  => 'Hauptstraße 23, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-12 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 152769398',
                'inbetriebnahme' => '2019-04-10',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 19-0346007-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Mischer, Thomas',
            'address'  => 'Brunnenstraße 6, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-12 + SMO 40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931052',
                'inbetriebnahme' => '2018-11-05',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 18-0346008-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Kramer, Andrea',
            'address'  => 'Mühlenstraße 12, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-16 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931060',
                'inbetriebnahme' => '2018-09-20',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 18-0346001-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Schneider, Markus',
            'address'  => 'Lindenweg 4, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-12 + SMO 20',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545678',
                'inbetriebnahme' => '2017-05-10',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 17-0346002-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Meier, Julia',
            'address'  => 'Ahornweg 8, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-8 + SMO S40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345678',
                'inbetriebnahme' => '2020-03-15',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 20-0346003-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Fischer, Peter',
            'address'  => 'Birkenweg 10, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-12 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931070',
                'inbetriebnahme' => '2018-07-22',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 18-0346010-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Hofmann, Sabine',
            'address'  => 'Eichenweg 2, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-8 + SMO 20',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545690',
                'inbetriebnahme' => '2017-09-12',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 17-0346004-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Berger, Lukas',
            'address'  => 'Rosenstraße 15, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-12 + SMO S40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345690',
                'inbetriebnahme' => '2020-10-05',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 20-0346007-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Vogel, Christian',
            'address'  => 'Fichtenweg 3, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-12 + SMO 40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931080',
                'inbetriebnahme' => '2018-10-30',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 18-0346012-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Peters, Anna',
            'address'  => 'Tulpenweg 7, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-12 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545700',
                'inbetriebnahme' => '2017-11-18',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 17-0346006-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Neumann, Daniel',
            'address'  => 'Schulstraße 5, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345700',
                'inbetriebnahme' => '2020-12-01',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 20-0346009-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Walther, Jana',
            'address'  => 'Gartenstraße 11, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-16 + SMO 40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931090',
                'inbetriebnahme' => '2018-08-25',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 18-0346014-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Otto, Frank',
            'address'  => 'Kirchweg 9, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545710',
                'inbetriebnahme' => '2017-06-14',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 17-0346008-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Richter, Nina',
            'address'  => 'Hegelstraße 3, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-12 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345710',
                'inbetriebnahme' => '2021-01-20',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 21-0346001-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Beck, Martin',
            'address'  => 'Lessingstraße 6, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-12 + SMO 40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931100',
                'inbetriebnahme' => '2019-02-28',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 19-0346002-003',
            ],
            ],
        ],
        [
            'name_raw' => 'König, Laura',
            'address'  => 'Mozartstraße 4, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-12 + SMO 20',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545720',
                'inbetriebnahme' => '2017-08-09',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 17-0346010-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Schulz, Jana',
            'address'  => 'Goethestraße 2, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-8 + SMO S40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345720',
                'inbetriebnahme' => '2021-03-11',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 21-0346003-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Krüger, Oliver',
            'address'  => 'Beethovenstraße 7, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-16 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931110',
                'inbetriebnahme' => '2019-04-18',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 19-0346004-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Hahn, Timo',
            'address'  => 'Bachstraße 10, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-8 + SMO 20',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545730',
                'inbetriebnahme' => '2017-10-03',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 17-0346012-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Weber, Lisa',
            'address'  => 'Schillerstraße 9, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-12 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345730',
                'inbetriebnahme' => '2021-05-07',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 21-0346005-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Wolf, Tim',
            'address'  => 'Buchenweg 1, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-12 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931120',
                'inbetriebnahme' => '2019-06-12',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 19-0346006-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Jäger, Kevin',
            'address'  => 'Ringstraße 8, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-12 + SMO 20',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545740',
                'inbetriebnahme' => '2017-12-20',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 17-0346014-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Seidel, Katharina',
            'address'  => 'Parkstraße 14, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-8 + SMO S40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345740',
                'inbetriebnahme' => '2021-07-19',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 21-0346007-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Keller, Fabian',
            'address'  => 'Heidestraße 12, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-16 + SMO 40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931130',
                'inbetriebnahme' => '2019-08-29',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 19-0346008-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Brinkmann, Susanne',
            'address'  => 'Waldweg 6, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545750',
                'inbetriebnahme' => '2018-02-13',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 18-0346001-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Arnold, Denise',
            'address'  => 'Schützenstraße 5, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-12 + SMO S40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345750',
                'inbetriebnahme' => '2021-09-23',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 21-0346009-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Eckert, Marvin',
            'address'  => 'Am Hang 13, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-12 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931140',
                'inbetriebnahme' => '2019-10-17',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 19-0346010-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Schwarz, Leonie',
            'address'  => 'Im Winkel 2, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-12 + SMO 20',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545760',
                'inbetriebnahme' => '2018-04-08',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 18-0346003-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Brandt, Jonas',
            'address'  => 'Talstraße 11, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345760',
                'inbetriebnahme' => '2021-11-04',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 21-0346011-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Albrecht, Kerstin',
            'address'  => 'Bergstraße 16, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-16 + SMO 40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931150',
                'inbetriebnahme' => '2020-01-09',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 20-0346001-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Roth, Florian',
            'address'  => 'Fliederweg 7, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545770',
                'inbetriebnahme' => '2018-06-21',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 18-0346005-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Busch, Jana',
            'address'  => 'Kirchplatz 3, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-12 + SMO S40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345770',
                'inbetriebnahme' => '2022-01-14',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 22-0346001-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Dietrich, Sven',
            'address'  => 'Poststraße 8, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-12 + SMO 40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931160',
                'inbetriebnahme' => '2020-03-03',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 20-0346003-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Hartmann, Nicole',
            'address'  => 'Jahnstraße 10, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-12 + SMO 20',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545780',
                'inbetriebnahme' => '2018-08-02',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 18-0346007-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Zimmermann, Paul',
            'address'  => 'Holzweg 4, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345780',
                'inbetriebnahme' => '2022-03-09',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 22-0346003-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Krause, Miriam',
            'address'  => 'Hauptweg 2, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-16 + SMO 40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931170',
                'inbetriebnahme' => '2020-05-27',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 20-0346005-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Funk, Andreas',
            'address'  => 'Mühlenweg 5, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545790',
                'inbetriebnahme' => '2018-10-15',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 18-0346009-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Heller, Tobias',
            'address'  => 'Am Markt 9, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-12 + SMO S40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345790',
                'inbetriebnahme' => '2022-05-20',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 22-0346005-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Hauser, Julia',
            'address'  => 'Nelkenweg 13, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-12 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931180',
                'inbetriebnahme' => '2020-07-11',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 20-0346007-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Paulsen, Carina',
            'address'  => 'Lindenplatz 6, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-12 + SMO 20',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545800',
                'inbetriebnahme' => '2019-01-05',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 19-0346001-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Möller, Jörg',
            'address'  => 'Feldweg 3, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345800',
                'inbetriebnahme' => '2022-07-28',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 22-0346007-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Franz, Melanie',
            'address'  => 'Birkenplatz 5, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-16 + SMO 40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931190',
                'inbetriebnahme' => '2020-09-03',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 20-0346009-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Urban, Patrick',
            'address'  => 'Ringweg 12, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545810',
                'inbetriebnahme' => '2019-03-18',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 19-0346003-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Pohl, David',
            'address'  => 'Schulweg 1, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-12 + SMO S40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345810',
                'inbetriebnahme' => '2022-09-16',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 22-0346009-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Kühne, Lara',
            'address'  => 'Mittelweg 7, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-12 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931200',
                'inbetriebnahme' => '2020-11-24',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 20-0346011-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Voigt, Henning',
            'address'  => 'Wiesenweg 4, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-12 + SMO 20',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545820',
                'inbetriebnahme' => '2019-05-29',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 19-0346005-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Seifert, Markus',
            'address'  => 'Brückenstraße 9, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345820',
                'inbetriebnahme' => '2022-11-08',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 22-0346011-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Herbst, Ingo',
            'address'  => 'Friedhofstraße 3, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-16 + SMO 40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931210',
                'inbetriebnahme' => '2021-01-18',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 21-0346001-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Horn, Vanessa',
            'address'  => 'Grüner Weg 11, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545830',
                'inbetriebnahme' => '2019-07-14',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 19-0346007-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Sauer, Felix',
            'address'  => 'Kampstraße 6, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-12 + SMO S40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345830',
                'inbetriebnahme' => '2023-01-12',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 23-0346001-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Winter, Lara',
            'address'  => 'Neuer Weg 8, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-12 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931220',
                'inbetriebnahme' => '2021-03-25',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 21-0346003-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Heinrich, Simon',
            'address'  => 'Pfarrstraße 2, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-12 + SMO 20',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545840',
                'inbetriebnahme' => '2019-09-09',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 19-0346009-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Marx, Jana',
            'address'  => 'Mühlenstraße 5, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345840',
                'inbetriebnahme' => '2023-03-03',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 23-0346003-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Kraft, Jonas',
            'address'  => 'Bahnhofstraße 30, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-16 + SMO 40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931230',
                'inbetriebnahme' => '2021-05-30',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 21-0346005-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Albers, Svea',
            'address'  => 'Hauptstraße 25, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545850',
                'inbetriebnahme' => '2019-11-17',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 19-0346011-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Hesse, Tanja',
            'address'  => 'Brunnenstraße 10, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-12 + SMO S40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345850',
                'inbetriebnahme' => '2023-05-22',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 23-0346005-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Brand, Nico',
            'address'  => 'Ringstraße 20, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-12 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931240',
                'inbetriebnahme' => '2021-07-18',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 21-0346007-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Kopp, Theresa',
            'address'  => 'Fichtenweg 15, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-12 + SMO 20',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545860',
                'inbetriebnahme' => '2020-01-30',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 20-0346001-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Raabe, Joana',
            'address'  => 'Rosenweg 9, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345860',
                'inbetriebnahme' => '2023-07-14',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 23-0346007-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Kästner, Nils',
            'address'  => 'Heinrichstraße 4, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-16 + SMO 40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931250',
                'inbetriebnahme' => '2021-09-06',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 21-0346009-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Behrens, Ella',
            'address'  => 'Parkweg 11, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545870',
                'inbetriebnahme' => '2020-03-25',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 20-0346003-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Ritter, Aaron',
            'address'  => 'Weststraße 7, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-12 + SMO S40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345870',
                'inbetriebnahme' => '2023-09-09',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 23-0346009-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Kunze, Fabio',
            'address'  => 'Nordstraße 2, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-12 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931260',
                'inbetriebnahme' => '2021-11-23',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 21-0346011-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Hirsch, Lotta',
            'address'  => 'Südstraße 8, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-12 + SMO 20',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545880',
                'inbetriebnahme' => '2020-05-18',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 20-0346005-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Eckhardt, Paula',
            'address'  => 'Oststraße 10, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345880',
                'inbetriebnahme' => '2023-11-16',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 23-0346011-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Köhler, Enno',
            'address'  => 'Im Dorfe 6, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-16 + SMO 40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931270',
                'inbetriebnahme' => '2022-01-31',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 22-0346001-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Haag, Line',
            'address'  => 'Grabenstraße 3, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545890',
                'inbetriebnahme' => '2020-07-06',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 20-0346007-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Rau, Jano',
            'address'  => 'Hinterstraße 11, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-12 + SMO S40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345890',
                'inbetriebnahme' => '2024-01-20',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 24-0346001-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Westermann, Jonne',
            'address'  => 'Mühlenrain 8, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-12 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931280',
                'inbetriebnahme' => '2022-03-23',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 22-0346003-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Mangold, Nevio',
            'address'  => 'Steinstraße 1, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-12 + SMO 20',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545900',
                'inbetriebnahme' => '2020-09-29',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 20-0346009-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Weidner, Nino',
            'address'  => 'Alter Kirchweg 2, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345900',
                'inbetriebnahme' => '2024-03-15',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 24-0346003-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Seidel, Jaro',
            'address'  => 'Neue Straße 4, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-16 + SMO 40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931290',
                'inbetriebnahme' => '2022-05-18',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 22-0346005-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Stahl, Joris',
            'address'  => 'Kampweg 9, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545910',
                'inbetriebnahme' => '2020-11-11',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 20-0346011-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Konrad, Justus',
            'address'  => 'Rathausplatz 3, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-12 + SMO S40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345910',
                'inbetriebnahme' => '2024-05-28',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 24-0346005-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Wegner, Kimi',
            'address'  => 'Alte Gasse 7, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-12 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931300',
                'inbetriebnahme' => '2022-07-25',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 22-0346007-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Hintz, Lino',
            'address'  => 'Schützenplatz 10, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-12 + SMO 20',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545920',
                'inbetriebnahme' => '2021-01-07',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 21-0346001-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Gruber, Lutz',
            'address'  => 'Bruchweg 6, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345920',
                'inbetriebnahme' => '2024-07-19',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 24-0346007-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Hölzer, Lenn',
            'address'  => 'Weidenweg 8, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-16 + SMO 40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931310',
                'inbetriebnahme' => '2022-09-13',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 22-0346009-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Ludwig, Levi',
            'address'  => 'Dorfplatz 5, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545930',
                'inbetriebnahme' => '2021-03-15',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 21-0346003-002',
            ],
            ],
        ],
        [
            'name_raw' => 'Ternes, Lias',
            'address'  => 'Fischerweg 2, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-12 + SMO S40',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345930',
                'inbetriebnahme' => '2024-09-11',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 24-0346009-003',
            ],
            ],
        ],
        [
            'name_raw' => 'Weiss, Luis',
            'address'  => 'Gerhartstraße 4, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2120-12 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 142931320',
                'inbetriebnahme' => '2022-11-28',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 22-0346011-004',
            ],
            ],
        ],
        [
            'name_raw' => 'Huth, Mio',
            'address'  => 'Sonnenweg 3, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'F2040-12 + SMO 20',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 132545940',
                'inbetriebnahme' => '2021-05-20',
                'kaufdatum' => null,
                'sa_re' => 'WS-RE: 21-0346005-001',
            ],
            ],
        ],
        [
            'name_raw' => 'Jäger, Neo',
            'address'  => 'Uhlandstraße 2, 33154 Salzkotten',
            'products' => [
            [
                'produkt' => 'S2125-8 + VVM 320',
                'hersteller' => 'NIBE',
                'seriennummer' => 'AS/1 162345940',
                'inbetriebnahme' => '2024-11-05',
                'kaufdatum' => null,
                'sa_re' => 'SA-RE: 24-0346011-002',
            ],
            ],
        ],
        ];

        DB::transaction(function () use ($entries, $heatpumpGroup, $defaultDepartmentId, $defaultServiceId, $defaultEmployeeId, $defaultFieldEmployeeId) {
            foreach ($entries as $entry) {
                [$lastname, $firstname] = $this->splitName($entry['name_raw']);

                $lead = NewLeads::create([
                    'customer_type'           => 'Privat',
                    'customer_no'             => null,
                    'title'                   => null,
                    'firma'                   => null,
                    'lastname'                => $lastname,
                    'name'                    => $firstname,
                    'full_address'            => $entry['address'],
                    'street'                  => null,
                    'latitude'                => null,
                    'longitude'               => null,
                    'polygon_height'          => null,
                    'polygon_width'           => null,
                    'polygon_area'            => null,
                    'elevation'               => null,
                    'postcode'                => null,
                    'city'                    => null,
                    'phone'                   => null,
                    'telephone'               => null,
                    'email'                   => null,
                    'source'                  => 'Bestand NIBE Wärmepumpe',
                    'contact_person'          => null, // set from auth()->user()->employee_id in app
                    'branch'                  => null,
                    'interest_rating'         => 0,
                    'seriousness_rating'      => 0,
                    'price_information'       => 0,
                    'status'                  => 'customer',
                    'status_msg'              => 'Bestandskunde Wärmepumpe',
                    'info'                    => null,
                    'purchase_status'         => 'completed',
                    'total_purchase'          => 0,
                    'default_project_minutes' => null,
                    'purchase_date'           => null,
                ]);

                $alternative = LeadAlternativeAdd::create([
                    'lead_id'          => $lead->id,
                    'full_address'     => $entry['address'],
                    'street'           => null,
                    'postcode'         => null,
                    'city'             => null,
                    'lat'              => null,
                    'lon'              => null,
                    'elevation'        => null,
                    'main'             => 1,
                    'address_no'       => 1,
                    'object_name'      => 'Bestand Wärmepumpe',
                    'request_date'     => null,
                    'periority'        => 'Normal',
                    'document'         => null,
                    'note'             => null,
                    'appointment'      => null,
                    'appointment_by'   => null,
                    'objective'        => 'Service / Wartung Wärmepumpe',
                    'living_space'     => null,
                    'unusable_space'   => null,
                    'number_people'    => null,
                    'number_we'        => null,
                    'number_stories'   => null,
                    'installation_location'       => null,
                    'installation_location_extra' => null,
                    'annual_consumption'          => null,
                    'tile_name'                   => null,
                    'roof_type'                   => null,
                    'roof_age'                    => null,
                    'house_year'                  => null,
                    'heating_system_age'          => null,
                    'heating_system_year'         => null,
                    'heating_type'                => null,
                    'heating_system_type'         => null,
                    'annual_heating_energy_consumption'     => null,
                    'annual_heating_energy_consumption_kwh' => null,
                    'electric_car'                => null,
                    'electric_car_plan'           => null,
                    'status'                      => 'Published',
                    'total_number'                => null,
                    'answered_number'             => null,
                    'roof_covering'               => null,
                    'roof_pitch'                  => null,
                    'roof_direction'              => null,
                    'fireplace'                   => null,
                    'wood_consumption'            => null,
                    'fireplace_value'             => null,
                    'car_kilo'                    => null,
                    'stage'                       => 'lead',
                    'project_date'                => null,
                    'object_remark'               => null,
                    'heating_remark'              => null,
                    'roof_remark'                 => null,
                    'energy_remark'               => null,
                    'car_remark'                  => null,
                    'wallbox_location'            => null,
                    'is_owner'                    => 'Ja',
                    'is_living_inside'            => 'Ja',
                    'income'                      => 40000,
                    'insolation'                  => 0,
                    'insolation_thickness'        => null,
                    'insolation_type'             => null,
                    'insolation_matarial'         => null,
                    'insolation_age'              => null,
                    'object_type'                 => null,
                    'building_condition'          => null,
                    'owner_count'                 => null,
                    'person_count'                => null,
                    'building_year'               => null,
                    'story_count'                 => null,
                    'heated_area'                 => null,
                    'external_insulation_thickness' => null,
                    'masonry'                     => null,
                    'window_glazing'              => null,
                    'window_frame'                => null,
                    'window_year'                 => null,
                    'door_year'                   => null,
                    'door_condition'              => null,
                    'chimney'                     => null,
                    'heating_circuits_count'      => null,
                    'pipe_system_count'           => null,
                    'pipe_system_material'        => null,
                    'quantity'                    => null,
                    'consumption'                 => null,
                    'bathroom_count'              => null,
                    'hot_water_generation'        => null,
                    'bathtub_count'               => null,
                    'income_level'                => null,
                    'total_heat_consumption'      => null,
                    'total_electricity_consumption' => null,
                    'heating_load_calculation'    => null,
                    'electric_car_count'          => null,
                    'wallbox_count'               => null,
                    'heavy_current_cable'         => null,
                    'network_cable'               => null,
                    'groundwork'                  => null,
                    'company_vehicle'             => null,
                    'bidirectional_car'           => null,
                    'power_household'             => null,
                    'power_heatpump'              => null,
                    'power_electric_car'          => null,
                    'power_other'                 => null,
                    'power_total'                 => null,
                    'meter_cabinet'               => null,
                    'meter_count'                 => null,
                    'tenant_model'                => null,
                    'installation_location_power' => null,
                    'network_wlan'                => null,
                    'usage_type'                  => null,
                    'income_taxed'                => null,
                    'heating_age_group'           => null,
                    'natural_refrigerant'         => null,
                    'investment_costs'            => null,
                    'calculated_subsidy'          => null,
                    'calculated_credit_need'      => null,
                    'calculated_rate'             => null,
                    'recommended_program'         => null,
                    'subsidy_quote'               => null,
                    'number_self_used'            => null,
                    'solar_module_kwp'            => null,
                    'solar_tile_kwp'              => null,
                    'battery_kwh'                 => null,
                    'balcony_modules'             => null,
                    'has_pump_upgrade'            => null,
                    'hydraulic_only'              => null,
                    'solar_thermal'               => null,
                    'solar_thermal_area'          => null,
                    'solar_thermal_simulation'    => null,
                ]);

                foreach ($entry['products'] as $product) {
                    LeadProductList::create([
                        'customer_id'      => $lead->id,
                        'alternative_id'   => $alternative->id,
                        'product_id'       => $heatpumpGroup->id,
                        'service_id'       => $defaultServiceId,
                        'department_id'    => $defaultDepartmentId,
                        'employee_id'      => $defaultEmployeeId,
                        'field_employee'   => $defaultFieldEmployeeId,
                        'service'          => 'complete',
                        'status'           => 'open',
                        'work_status'      => 'playing',
                        'interest'         => 'intent',
                        'realization_time' => null,
                        'stage_history'    => null,
                        'stage'            => 'lead',
                        'old_stage'        => null,
                        'price'            => null,
                        'price_latest'     => null,
                        'project_minutes'  => null,
                        'price_history'    => null,
                    ]);
                }
            }
        });
    }

    private function splitName(string $name): array
    {
        $parts = array_map('trim', explode(',', $name, 2));

        if (count($parts) === 2) {
            return [$parts[0], $parts[1]];
        }

        $chunks = preg_split('/\s+/', $name, 2);
        if (count($chunks) === 2) {
            return [$chunks[1], $chunks[0]];
        }

        return [$name, null];
    }
}

