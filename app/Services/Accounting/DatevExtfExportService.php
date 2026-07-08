<?php

namespace App\Services\Accounting;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * FiBu Stufe (vi) — DATEV-EXTF-Export (Format „Buchungsstapel", Kategorie 21, Version 13).
 *
 * Erzeugt aus den festgeschriebenen Journal-Buchungen eines Zeitraums die EXTF-CSV
 * (CP1252, Semikolon-getrennt, Dezimal-Komma, Beträge ohne Vorzeichen + S/H-Kennzeichen).
 *
 * Buchungs-Abbildung (Stern-Zerlegung): eine Buchung mit genau EINER Zeile auf einer Seite
 * (z. B. Debitor an {Erlös, USt}) wird als DATEV-Zeilen abgebildet — Pivot-Konto = Gegenkonto,
 * je Gegenzeile eine Buchungszeile (Umsatz, Konto, S/H). Die Steuer wird als eigene Zeile geführt
 * (manuelle Steuer, kein BU-Automatik-Schlüssel → keine Doppelzählung). Komplexe n:m-Buchungen
 * werden als nicht exportierbar gemeldet (in den bisherigen Belegfluss-/Zahlungs-Buchungen kommen
 * sie nicht vor).
 *
 * ⚠️ Betriebsordnung 2.3: **Versand nur durch Yama.** Dieser Service erzeugt/prüft die Datei;
 * er verschickt nichts. Berater-/Mandantennummern kommen aus accounting_clients.
 */
class DatevExtfExportService
{
    /** Spaltenüberschriften Buchungsstapel v13 (Kernumfang; DATEV toleriert Kernspalten führend). */
    private const CAPTIONS = [
        'Umsatz (ohne Soll/Haben-Kz)', 'Soll/Haben-Kennzeichen', 'WKZ Umsatz', 'Kurs', 'Basis-Umsatz',
        'WKZ Basis-Umsatz', 'Konto', 'Gegenkonto (ohne BU-Schlüssel)', 'BU-Schlüssel', 'Belegdatum',
        'Belegfeld 1', 'Belegfeld 2', 'Skonto', 'Buchungstext',
    ];

    /**
     * Baut die EXTF-Datei als CP1252-String. Setzt datev_export_status NICHT (das ist ein eigener,
     * von Yama beim tatsächlichen Export/Versand auszulösender Schritt).
     */
    public function exportBuchungsstapel(int $clientId, string $von, string $bis, string $bezeichnung = 'Buchungsstapel'): string
    {
        $client = DB::table('accounting_clients')->find($clientId);
        if ($client === null) {
            throw new RuntimeException("Mandant #{$clientId} nicht gefunden.");
        }

        $entries = DB::table('accounting_journal_entries')
            ->where('accounting_client_id', $clientId)->where('is_finalized', true)
            ->whereBetween('booking_date', [$von, $bis])
            ->orderBy('booking_date')->orderBy('id')->get();

        $dataRows = [];
        foreach ($entries as $entry) {
            foreach ($this->zeilenFuerBuchung($entry) as $row) {
                $dataRows[] = $row;
            }
        }

        $utf8 = $this->headerZeile($client, $von, $bis, $bezeichnung)."\r\n"
            .$this->csvZeile(self::CAPTIONS)."\r\n";
        foreach ($dataRows as $r) {
            $utf8 .= $this->csvZeile($r)."\r\n";
        }

        // DATEV erwartet Windows-1252.
        return mb_convert_encoding($utf8, 'CP1252', 'UTF-8');
    }

    /** Stern-Zerlegung einer Buchung in DATEV-Datenzeilen. */
    private function zeilenFuerBuchung(object $entry): array
    {
        $lines = DB::table('accounting_journal_lines as l')
            ->join('accounts as a', 'a.id', '=', 'l.account_id')
            ->where('l.journal_entry_id', $entry->id)->orderBy('l.line_number')
            ->select('l.debit_amount', 'l.credit_amount', 'a.account_number')->get();

        $soll = $lines->filter(fn ($l) => (float) $l->debit_amount > 0)->values();
        $haben = $lines->filter(fn ($l) => (float) $l->credit_amount > 0)->values();

        // Pivot = Seite mit genau einer Zeile (Gegenkonto). Gegenzeilen = die andere Seite.
        if ($soll->count() === 1) {
            $pivot = $soll[0]->account_number;
            $gegen = $haben;
            $kennzeichen = 'H'; // Umsatz wirkt als Haben auf das Konto (Gegenzeile)
        } elseif ($haben->count() === 1) {
            $pivot = $haben[0]->account_number;
            $gegen = $soll;
            $kennzeichen = 'S';
        } else {
            throw new RuntimeException("Buchung #{$entry->id}: komplexe n:m-Buchung, DATEV-Stern-Zerlegung nicht möglich.");
        }

        $belegdatum = $this->ttmm($entry->document_date ?? $entry->booking_date);
        $belegfeld = (string) ($entry->document_number ?? $entry->booking_number ?? $entry->id);
        $text = mb_substr((string) ($entry->booking_text ?? ''), 0, 60);

        $rows = [];
        foreach ($gegen as $g) {
            $betrag = (float) ($kennzeichen === 'H' ? $g->credit_amount : $g->debit_amount);
            $rows[] = [
                $this->betrag($betrag), $kennzeichen, 'EUR', '', '', '',
                $g->account_number, $pivot, '', $belegdatum, $belegfeld, '', '', $text,
            ];
        }

        return $rows;
    }

    /** EXTF-Kopfzeile (31 Felder). */
    private function headerZeile(object $client, string $von, string $bis, string $bezeichnung): string
    {
        $wjBeginn = substr($von, 0, 4).str_pad((string) ($client->fiscal_year_start_month ?? 1), 2, '0', STR_PAD_LEFT).'01';
        $felder = [
            'EXTF', '700', '21', 'Buchungsstapel', '13',
            now()->format('YmdHisv'),   // erzeugt am
            '', '', '', '',             // importiert/Herkunft/exportiert von/importiert von
            (string) ($client->datev_berater_nr ?? ''),
            (string) ($client->datev_mandant_nr ?? ''),
            $wjBeginn, '4',             // WJ-Beginn, Sachkontenlänge
            str_replace('-', '', $von), str_replace('-', '', $bis),
            $bezeichnung, '', '1', '',  // Bezeichnung, Diktatkürzel, Buchungstyp=1(FiBu), Rechnungslegungszweck
            '0', 'EUR', '', '', '', '', '', '', '', '',
        ];

        return $this->csvZeile($felder);
    }

    /** DATEV-Betrag: positiv, zwei Nachkommastellen, Dezimal-Komma, ohne Tausenderpunkt. */
    private function betrag(float $v): string
    {
        return number_format(abs($v), 2, ',', '');
    }

    /** Belegdatum als TTMM (DATEV-Standard im Buchungsstapel). */
    private function ttmm(?string $date): string
    {
        return $date ? substr($date, 8, 2).substr($date, 5, 2) : '';
    }

    /** Eine CSV-Zeile: Textfelder in Anführungszeichen, Zahlen/Kennzeichen ohne. */
    private function csvZeile(array $felder): string
    {
        return implode(';', array_map(function ($f) {
            if ($f === '' || is_numeric(str_replace(',', '.', (string) $f)) && ! in_array($f, ['S', 'H'], true)) {
                // Reine Zahlen (inkl. Beträge mit Komma) und Leerfelder ohne Quotes.
                return preg_match('/^-?\d+(,\d+)?$/', (string) $f) || $f === '' ? (string) $f : '"'.$f.'"';
            }

            return '"'.str_replace('"', '', (string) $f).'"';
        }, $felder));
    }

    /**
     * Konformitäts-Prüfer: validiert eine EXTF-Datei strukturell. Gibt Fehlerliste zurück (leer = konform).
     *
     * @return string[]
     */
    public function pruefeKonformitaet(string $extfCp1252): array
    {
        $fehler = [];
        $utf8 = mb_convert_encoding($extfCp1252, 'UTF-8', 'CP1252');
        $zeilen = array_values(array_filter(explode("\r\n", $utf8), fn ($z) => $z !== ''));

        if (empty($zeilen)) {
            return ['Datei ist leer.'];
        }
        // Kopf.
        $kopf = str_getcsv($zeilen[0], ';', '"', '\\');
        if (($kopf[0] ?? '') !== 'EXTF') {
            $fehler[] = 'Kopf: Feld 1 muss "EXTF" sein.';
        }
        if (($kopf[2] ?? '') !== '21' || ($kopf[3] ?? '') !== 'Buchungsstapel') {
            $fehler[] = 'Kopf: Format-Kategorie 21/"Buchungsstapel" fehlt.';
        }
        if (($kopf[4] ?? '') !== '13') {
            $fehler[] = 'Kopf: Format-Version 13 erwartet.';
        }
        if (count($kopf) < 22) {
            $fehler[] = 'Kopf: zu wenige Felder (<22).';
        }
        if (($kopf[21] ?? '') !== 'EUR') {
            $fehler[] = 'Kopf: WKZ (Feld 22) muss EUR sein.';
        }
        if (count($zeilen) < 2) {
            $fehler[] = 'Überschriftenzeile fehlt.';
        }
        // Datenzeilen ab Zeile 3.
        for ($i = 2; $i < count($zeilen); $i++) {
            $f = str_getcsv($zeilen[$i], ';', '"', '\\');
            $nr = $i + 1;
            if (! preg_match('/^\d+,\d{2}$/', $f[0] ?? '')) {
                $fehler[] = "Zeile {$nr}: Umsatz '{$f[0]}' nicht im Format 0,00.";
            }
            if (! in_array($f[1] ?? '', ['S', 'H'], true)) {
                $fehler[] = "Zeile {$nr}: Soll/Haben-Kennzeichen ungültig ('".($f[1] ?? '')."').";
            }
            if (! ctype_digit((string) ($f[6] ?? ''))) {
                $fehler[] = "Zeile {$nr}: Konto nicht numerisch.";
            }
            if (! ctype_digit((string) ($f[7] ?? ''))) {
                $fehler[] = "Zeile {$nr}: Gegenkonto nicht numerisch.";
            }
            if (! preg_match('/^\d{4}$/', $f[9] ?? '')) {
                $fehler[] = "Zeile {$nr}: Belegdatum nicht TTMM.";
            }
        }

        return $fehler;
    }
}
