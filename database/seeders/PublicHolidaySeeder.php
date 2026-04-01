<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublicHolidaySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $rows = [];

        $add = function (
            string $name,
            string $date,
            ?string $state = null,
            ?string $comment = null,
            ?string $city = null,
            string $country = 'Germany'
        ) use (&$rows, $now) {
            $rows[] = [
                'name'       => $name,
                'comment'    => $comment,
                'start_date' => $date,
                'end_date'   => $date,
                'city'       => $city,
                'state'      => $state,
                'country'    => $country,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        };

        $states = [
            'BW' => 'Baden-Württemberg',
            'BY' => 'Bavaria',
            'BE' => 'Berlin',
            'BB' => 'Brandenburg',
            'HB' => 'Bremen',
            'HH' => 'Hamburg',
            'HE' => 'Hesse',
            'MV' => 'Mecklenburg-Vorpommern',
            'NI' => 'Lower Saxony',
            'NW' => 'North Rhine-Westphalia',
            'RP' => 'Rhineland-Palatinate',
            'SL' => 'Saarland',
            'SN' => 'Saxony',
            'ST' => 'Saxony-Anhalt',
            'SH' => 'Schleswig-Holstein',
            'TH' => 'Thuringia',
        ];

        $years = [
            2026 => [
                'new_year'        => '2026-01-01',
                'good_friday'     => '2026-04-03',
                'easter_monday'   => '2026-04-06',
                'labour_day'      => '2026-05-01',
                'ascension_day'   => '2026-05-14',
                'whit_monday'     => '2026-05-25',
                'unity_day'       => '2026-10-03',
                'christmas_1'     => '2026-12-25',
                'christmas_2'     => '2026-12-26',

                'epiphany'        => '2026-01-06',
                'womens_day'      => '2026-03-08',
                'corpus_christi'  => '2026-06-04',
                'assumption_day'  => '2026-08-15',
                'childrens_day'   => '2026-09-20',
                'reformation_day' => '2026-10-31',
                'all_saints_day'  => '2026-11-01',
                'repentance_day'  => '2026-11-18',
            ],
            2027 => [
                'new_year'        => '2027-01-01',
                'good_friday'     => '2027-03-26',
                'easter_monday'   => '2027-03-29',
                'labour_day'      => '2027-05-01',
                'ascension_day'   => '2027-05-06',
                'whit_monday'     => '2027-05-17',
                'unity_day'       => '2027-10-03',
                'christmas_1'     => '2027-12-25',
                'christmas_2'     => '2027-12-26',

                'epiphany'        => '2027-01-06',
                'womens_day'      => '2027-03-08',
                'corpus_christi'  => '2027-05-27',
                'assumption_day'  => '2027-08-15',
                'childrens_day'   => '2027-09-20',
                'reformation_day' => '2027-10-31',
                'all_saints_day'  => '2027-11-01',
                'repentance_day'  => '2027-11-17',
            ],
            2028 => [
                'new_year'        => '2028-01-01',
                'good_friday'     => '2028-04-14',
                'easter_monday'   => '2028-04-17',
                'labour_day'      => '2028-05-01',
                'ascension_day'   => '2028-05-25',
                'whit_monday'     => '2028-06-05',
                'unity_day'       => '2028-10-03',
                'christmas_1'     => '2028-12-25',
                'christmas_2'     => '2028-12-26',

                'epiphany'        => '2028-01-06',
                'womens_day'      => '2028-03-08',
                'corpus_christi'  => '2028-06-15',
                'assumption_day'  => '2028-08-15',
                'childrens_day'   => '2028-09-20',
                'reformation_day' => '2028-10-31',
                'all_saints_day'  => '2028-11-01',
                'repentance_day'  => '2028-11-22',
            ],
        ];

        foreach ($years as $year => $d) {
            // Nationwide holidays
            $add('New Year\'s Day', $d['new_year'], null, "Nationwide public holiday in Germany ({$year})");
            $add('Good Friday', $d['good_friday'], null, "Nationwide public holiday in Germany ({$year})");
            $add('Easter Monday', $d['easter_monday'], null, "Nationwide public holiday in Germany ({$year})");
            $add('Labour Day', $d['labour_day'], null, "Nationwide public holiday in Germany ({$year})");
            $add('Ascension Day', $d['ascension_day'], null, "Nationwide public holiday in Germany ({$year})");
            $add('Whit Monday', $d['whit_monday'], null, "Nationwide public holiday in Germany ({$year})");
            $add('Day of German Unity', $d['unity_day'], null, "Nationwide public holiday in Germany ({$year})");
            $add('Christmas Day', $d['christmas_1'], null, "Nationwide public holiday in Germany ({$year})");
            $add('Second Day of Christmas', $d['christmas_2'], null, "Nationwide public holiday in Germany ({$year})");

            // Epiphany
            foreach (['BW', 'BY', 'ST'] as $code) {
                $add('Epiphany', $d['epiphany'], $states[$code], "State public holiday in {$states[$code]} ({$year})");
            }

            // International Women's Day
            foreach (['BE', 'MV'] as $code) {
                $add('International Women\'s Day', $d['womens_day'], $states[$code], "State public holiday in {$states[$code]} ({$year})");
            }

            // Corpus Christi
            foreach (['BW', 'BY', 'HE', 'NW', 'RP', 'SL'] as $code) {
                $add('Corpus Christi', $d['corpus_christi'], $states[$code], "State public holiday in {$states[$code]} ({$year})");
            }

            // Assumption Day
            $add('Assumption Day', $d['assumption_day'], $states['SL'], "State public holiday in Saarland ({$year})");

            // World Children's Day
            $add('World Children\'s Day', $d['childrens_day'], $states['TH'], "State public holiday in Thuringia ({$year})");

            // Reformation Day
            foreach (['BB', 'HB', 'HH', 'MV', 'NI', 'SH', 'SN', 'ST', 'TH'] as $code) {
                $add('Reformation Day', $d['reformation_day'], $states[$code], "State public holiday in {$states[$code]} ({$year})");
            }

            // All Saints' Day
            foreach (['BW', 'BY', 'NW', 'RP', 'SL'] as $code) {
                $add('All Saints\' Day', $d['all_saints_day'], $states[$code], "State public holiday in {$states[$code]} ({$year})");
            }

            // Repentance and Prayer Day
            $add('Repentance and Prayer Day', $d['repentance_day'], $states['SN'], "State public holiday in Saxony ({$year})");
        }

        DB::table('public_holidays')->insert($rows);
    }
}