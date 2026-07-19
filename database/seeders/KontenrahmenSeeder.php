<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * FiBu Stufe (ii) — Kontenrahmen-Seed (SKR03-Default + SKR04-fähig) + USt-Steuercodes + mapping_keys.
 *
 * Funktionaler Grundstock (nicht der volle 1500-Konten-SKR): die für die Belegkette/Buchungs-Engine
 * nötigen Sammel-, Steuer- und Erlöskonten je Kontenrahmen + Standard-USt-Codes (19/7/0, Vorsteuer 19/7)
 * + semantische account_mappings (mapping_key → Konto; KEINE harten Kontonummern im Buchungs-Code).
 *
 * Additiv/idempotent (updateOrInsert auf natürliche Schlüssel), Marker imported_from='skr_seed' für
 * restlosen Rückbau. SKR03 ist Default (Betriebsordnung-Weiche „SKR03-Default mit SKR04-Fähigkeit").
 */
class KontenrahmenSeeder extends Seeder
{
    private const MARKER = 'skr_seed';

    /** SKR03: [nummer, name, typ, kategorie, normalsaldo, steuerrelevant] */
    private const KONTEN = [
        'SKR03' => [
            ['1000', 'Kasse', 'aktiv', 'umlaufvermoegen', 'soll', false],
            ['1200', 'Bank', 'aktiv', 'umlaufvermoegen', 'soll', false],
            ['1400', 'Forderungen aus Lieferungen und Leistungen (Debitoren-Sammelkonto)', 'aktiv', 'forderungen', 'soll', false],
            ['1576', 'Abziehbare Vorsteuer 19 %', 'aktiv', 'vorsteuer', 'soll', true],
            ['1571', 'Abziehbare Vorsteuer 7 %', 'aktiv', 'vorsteuer', 'soll', true],
            ['1600', 'Verbindlichkeiten aus Lieferungen und Leistungen (Kreditoren-Sammelkonto)', 'passiv', 'verbindlichkeiten', 'haben', false],
            ['1776', 'Umsatzsteuer 19 %', 'passiv', 'umsatzsteuer', 'haben', true],
            ['1771', 'Umsatzsteuer 7 %', 'passiv', 'umsatzsteuer', 'haben', true],
            ['8400', 'Erlöse 19 % USt', 'erloes', 'umsatzerloese', 'haben', true],
            ['8300', 'Erlöse 7 % USt', 'erloes', 'umsatzerloese', 'haben', true],
            ['8200', 'Erlöse (steuerfrei / nicht steuerbar)', 'erloes', 'umsatzerloese', 'haben', true],
            ['3400', 'Wareneingang 19 % Vorsteuer', 'aufwand', 'wareneinsatz', 'soll', true],
            ['4980', 'Bürobedarf', 'aufwand', 'sonstige_aufwendungen', 'soll', true],
        ],
        'SKR04' => [
            ['1600', 'Kasse', 'aktiv', 'umlaufvermoegen', 'soll', false],
            ['1800', 'Bank', 'aktiv', 'umlaufvermoegen', 'soll', false],
            ['1200', 'Forderungen aus Lieferungen und Leistungen (Debitoren-Sammelkonto)', 'aktiv', 'forderungen', 'soll', false],
            ['1406', 'Abziehbare Vorsteuer 19 %', 'aktiv', 'vorsteuer', 'soll', true],
            ['1401', 'Abziehbare Vorsteuer 7 %', 'aktiv', 'vorsteuer', 'soll', true],
            ['3300', 'Verbindlichkeiten aus Lieferungen und Leistungen (Kreditoren-Sammelkonto)', 'passiv', 'verbindlichkeiten', 'haben', false],
            ['3806', 'Umsatzsteuer 19 %', 'passiv', 'umsatzsteuer', 'haben', true],
            ['3801', 'Umsatzsteuer 7 %', 'passiv', 'umsatzsteuer', 'haben', true],
            ['4400', 'Erlöse 19 % USt', 'erloes', 'umsatzerloese', 'haben', true],
            ['4300', 'Erlöse 7 % USt', 'erloes', 'umsatzerloese', 'haben', true],
            ['4200', 'Erlöse (steuerfrei / nicht steuerbar)', 'erloes', 'umsatzerloese', 'haben', true],
            ['5400', 'Wareneingang 19 % Vorsteuer', 'aufwand', 'wareneinsatz', 'soll', true],
            ['6815', 'Bürobedarf', 'aufwand', 'sonstige_aufwendungen', 'soll', true],
        ],
    ];

    /** USt-Codes: [code, name, tax_type, tax_direction, rate, vorsteuer_kontokey, umsatzsteuer_kontokey] */
    private const STEUERN = [
        ['UST19', 'Umsatzsteuer 19 %', 'normal', 'umsatzsteuer', 19.00, null, 'ust_19'],
        ['UST7', 'Umsatzsteuer 7 %', 'ermaessigt', 'umsatzsteuer', 7.00, null, 'ust_7'],
        ['UST0', 'Steuerfrei / nicht steuerbar', 'steuerfrei', 'keine', 0.00, null, null],
        ['VST19', 'Vorsteuer 19 %', 'normal', 'vorsteuer', 19.00, 'vorsteuer_19', null],
        ['VST7', 'Vorsteuer 7 %', 'ermaessigt', 'vorsteuer', 7.00, 'vorsteuer_7', null],
    ];

    /** mapping_key → Kontonummer je Kontenrahmen (semantische Buchungs-Auflösung). */
    private const MAPPINGS = [
        'SKR03' => [
            'erloes_19' => '8400', 'erloes_7' => '8300', 'erloes_steuerfrei' => '8200',
            'forderung_debitor' => '1400', 'verbindlichkeit_kreditor' => '1600',
            'ust_19' => '1776', 'ust_7' => '1771', 'vorsteuer_19' => '1576', 'vorsteuer_7' => '1571',
            'bank' => '1200', 'kasse' => '1000', 'wareneingang_19' => '3400',
        ],
        'SKR04' => [
            'erloes_19' => '4400', 'erloes_7' => '4300', 'erloes_steuerfrei' => '4200',
            'forderung_debitor' => '1200', 'verbindlichkeit_kreditor' => '3300',
            'ust_19' => '3806', 'ust_7' => '3801', 'vorsteuer_19' => '1406', 'vorsteuer_7' => '1401',
            'bank' => '1800', 'kasse' => '1600', 'wareneingang_19' => '5400',
        ],
    ];

    public function run(): void
    {
        $now = now();

        // 1) Kontenrahmen (SKR03 Default, SKR04).
        $charts = [];
        foreach (['SKR03' => true, 'SKR04' => false] as $code => $isDefault) {
            DB::table('chart_of_accounts')->updateOrInsert(
                ['code' => $code],
                ['name' => 'DATEV '.$code, 'country' => 'DE', 'version' => '2026', 'is_default' => $isDefault,
                    'is_active' => true, 'imported_from' => self::MARKER, 'updated_at' => $now, 'created_at' => $now],
            );
            $charts[$code] = DB::table('chart_of_accounts')->where('code', $code)->value('id');
        }

        // 2) Demo-Mandant (für client-skalierte Konten/Steuer/Mappings), Default SKR03.
        DB::table('accounting_clients')->updateOrInsert(
            ['name' => 'Demo-Mandant'],
            ['legal_name' => 'Solar Aspekt Nord GmbH', 'default_chart_of_account_id' => $charts['SKR03'],
                'default_currency' => 'EUR', 'fiscal_year_start_month' => 1, 'is_active' => true,
                'is_datev_enabled' => false, 'imported_from' => self::MARKER, 'updated_at' => $now, 'created_at' => $now],
        );
        $clientId = DB::table('accounting_clients')->where('name', 'Demo-Mandant')->value('id');

        // 3) Konten je Kontenrahmen.
        $accountId = []; // [chartCode][nummer] => id
        foreach (self::KONTEN as $chartCode => $konten) {
            foreach ($konten as [$nr, $name, $typ, $kat, $saldo, $steuer]) {
                DB::table('accounts')->updateOrInsert(
                    ['chart_of_account_id' => $charts[$chartCode], 'accounting_client_id' => $clientId, 'account_number' => $nr],
                    ['account_name' => $name, 'account_type' => $typ, 'account_category' => $kat,
                        'normal_balance' => $saldo, 'is_tax_relevant' => $steuer, 'is_active' => true,
                        'imported_from' => self::MARKER, 'updated_at' => $now, 'created_at' => $now],
                );
                $accountId[$chartCode][$nr] = DB::table('accounts')
                    ->where('chart_of_account_id', $charts[$chartCode])->where('account_number', $nr)->value('id');
            }
        }

        // 4) USt-Steuercodes (Konten-Verknüpfung über SKR03-Default-Mandant).
        foreach (self::STEUERN as [$code, $name, $type, $dir, $rate, $vstKey, $ustKey]) {
            $inputAcc = $vstKey ? ($accountId['SKR03'][self::MAPPINGS['SKR03'][$vstKey]] ?? null) : null;
            $outputAcc = $ustKey ? ($accountId['SKR03'][self::MAPPINGS['SKR03'][$ustKey]] ?? null) : null;
            DB::table('tax_codes')->updateOrInsert(
                ['accounting_client_id' => $clientId, 'code' => $code],
                ['name' => $name, 'tax_type' => $type, 'tax_direction' => $dir, 'rate_percent' => $rate,
                    'input_tax_account_id' => $inputAcc, 'output_tax_account_id' => $outputAcc,
                    'is_active' => true, 'imported_from' => self::MARKER, 'updated_at' => $now, 'created_at' => $now],
            );
        }

        // 5) account_mappings (mapping_key → Konto je Kontenrahmen).
        $mCount = 0;
        foreach (self::MAPPINGS as $chartCode => $keys) {
            foreach ($keys as $key => $nr) {
                $accId = $accountId[$chartCode][$nr] ?? null;
                if ($accId === null) {
                    continue;
                }
                DB::table('account_mappings')->updateOrInsert(
                    ['accounting_client_id' => $clientId, 'chart_of_account_id' => $charts[$chartCode], 'mapping_key' => $key, 'applies_to' => ''],
                    ['mapping_name' => $key, 'account_id' => $accId, 'priority' => 100, 'is_active' => true,
                        'imported_from' => self::MARKER, 'updated_at' => $now, 'created_at' => $now],
                );
                $mCount++;
            }
        }

        $this->command?->info('Kontenrahmen-Seed: 2 Kontenrahmen (SKR03 Default/SKR04), 1 Mandant, '
            .(count(self::KONTEN['SKR03']) + count(self::KONTEN['SKR04'])).' Konten, '
            .count(self::STEUERN).' USt-Codes, '.$mCount.' Mappings (Marker='.self::MARKER.').');
    }
}
