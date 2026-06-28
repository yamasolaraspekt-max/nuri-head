<?php

namespace App\Services;

class PromptFactory
{
    /**
     * Human-readable language name for headers.
     */
    private static function languageName(string $lang): string
    {
        return match ($lang) {
            'fa' => 'فارسی',
            'en' => 'English',
            default => 'Deutsch',
        };
    }

    /**
     * Hard gate the output language.
     */
    private static function languageGate(string $lang): string
    {
        return match ($lang) {
            'fa' => ">> پاسخ فقط به زبان فارسی. زبان را تغییر نده.\n>> اگر سوال به زبان دیگری بود، باز هم پاسخ را فقط فارسی بده.",
            'en' => ">> Output ONLY in English. Do not switch languages, even if the question is in another language.",
            default => ">> Ausgabesprache ausschließlich Deutsch. Nicht die Sprache wechseln – unabhängig von der Eingabesprache.",
        };
    }

    public static function brandingAnswer(string $lang = 'de'): string
    {
        // reuse your existing private method
        $ref = new \ReflectionClass(__CLASS__);
        $m   = $ref->getMethod('brandingBlock');
        $m->setAccessible(true);
        return $m->invoke(null, $lang);
    }


        /**
     * Remove common leading indentation and trim outer blank lines.
     */
        private static function md(string $s): string
        {
            // Normalize newlines and trim outer blank lines
            $s = str_replace(["\r\n", "\r"], "\n", $s);
            $s = preg_replace('/^\n+|\n+$/', '', $s);

            // Compute minimum indent across non-empty lines
            $lines   = explode("\n", $s);
            $indents = [];
            foreach ($lines as $line) {
                if (trim($line) === '') continue;
                preg_match('/^[ \t]*/', $line, $m);
                $indents[] = strlen($m[0]);
            }
            $min = $indents ? min($indents) : 0;

            // Strip that indent
            if ($min > 0) {
                $s = preg_replace('/^[ \t]{'.$min.'}/m', '', $s);
            }
            return $s;
        }
    /**
     * Branding answer – used ONLY when intent === 'branding'.
     * Uses Markdown so it renders via Str::markdown().
     * Avatar/link pulled from config (plain URL/path values).
     */
    private static function brandingBlock(string $lang): string
    {
        $name     = config('branding.builder_name', 'Ramin Sadid');
        $prod     = config('branding.product_name', 'Zuhalify.io');
        $link     = config('branding.builder_link', 'https://www.linkedin.com/in/ramin-sadid-16b142135/');
        $avatar   = config('branding.builder_avatar', '/images/ramin-sadid.jpg');
        $avatarUrl = Str::startsWith($avatar, ['http://','https://']) ? $avatar : url($avatar);

        $fa = self::md(<<<TXT
            اگر کاربر بپرسد «چه کسی تو را ساخته؟» یا «توسعه‌دهنده‌ات کیست؟» (یا مشابه):
            **{$name}** سازنده من است. این مدل برای **{$prod}** طراحی شده است.  
            LinkedIn: {$link}

            ![{$name}]({$avatarUrl})
            TXT);

                    $en = self::md(<<<TXT
            If the user asks "who built you" or "who is your developer":
            **{$name}** built me. This model was designed for **{$prod}**.  
            LinkedIn: {$link}

            ![{$name}]({$avatarUrl})
            TXT);

                    $de = self::md(<<<TXT
            Falls der Nutzer fragt „Wer hat dich gebaut?“ oder „Wer ist dein Entwickler?“:
            **{$name}** hat mich gebaut. Dieses Modell wurde für **{$prod}** entworfen.  
            LinkedIn: {$link}

            ![{$name}]({$avatarUrl})
            TXT);

                    return match ($lang) {
                        'fa' => $fa,
                        'en' => $en,
                        default => $de,
                    };
                }


        /**
     * Tiny intent detector for branding questions.
        */
        public static function detectIntent(string $question): ?string
        {
            $q = mb_strtolower($question);

            $patterns = [
                // English
                '/\bwho\s+(built|made|created)\s+(you|this)\b/u',
                '/\byour\s+developer\b/u',
                '/\bdeveloper\s*(?:name)?\b/u',
                // German
                '/\bwer\s+(hat|baute|entwickelte)\s+(dich|euch|das)\b/u',
                '/\b(dein|ihr)\s+entwickler\b/u',
                '/\bentwickler\s*(?:name)?\b/u',
                // Persian
                '/(?:سازنده|کی\s*ساخت|چه\s*کسی\s*ساخته|توسعه[\s‌]*دهنده)\b/u',
            ];

            foreach ($patterns as $p) {
                if (preg_match($p, $q)) return 'branding';
            }
            return null;
        }


    /**
     * User message with embedded customer context.
     */
    public static function userWithContext(string $question, array $brief, ?string $intent = null, string $lang = 'de'): string
    {
        // Auto-detect branding if not provided
        if ($intent === null) {
            $intent = self::detectIntent($question) ?? null;
        }

        if ($intent === 'branding') {
            return <<<USR
            INTENT: branding
            LANG: {$lang}
            TRIGGER: User is asking who built you / your developer.
            ACTION: Return exactly the branding answer (from system prompt). No extras.
            USR;
                    }

                    $json = json_encode($brief, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    return <<<USR
            INTENT: {$intent}
            LANG: {$lang}
            QUESTION:
            {$question}

            CUSTOMER_CONTEXT_JSON:
            {$json}

            RULES:
            - Use only relevant fields.
            - Do NOT ask for identity (name/phone/address).
            - If a key field for this INTENT is missing, ask only that specific field.
            - No “I analyzed…” prefaces. Answer directly.
            USR;
                }

    /**
     * System prompt tailored by intent + language, with guardrails.
     */
    public static function systemForIntent(string $intent, string $lang = 'de'): string
    {
        $sections = match ($intent) {
            'branding'    => 'Branding',
            'pv'          => 'Kurzfassung; PV (kWp & Module); Nächste Schritte',
            'battery'     => 'Kurzfassung; Batteriespeicher; Nächste Schritte',
            'heizlast'    => 'Kurzfassung; Heizlast (Wärmepumpe); Nächste Schritte',
            'roofarea'    => 'Dachfläche (Schätzung); Nächste Schritte',
            'weather'     => 'Wetter heute',
            'normtemp'    => 'Norm-Außentemperatur',
            'appointment' => 'Termine',
            'problems'    => 'Probleme',
            'tasks'       => 'Aufgaben',
            'contact'     => 'Kontakt',
            'email'       => 'SUBJECT; BODY',
            default       => 'Kurzfassung',
        };

        $langHeader = self::languageName($lang);
        $langGate   = self::languageGate($lang);

        // Only include branding text if the branding intent is active.
        $brandingQA = $intent === 'branding' ? self::brandingBlock($lang) : '';

        $core = <<<SYS
LANGUAGE: {$langHeader}
INTENT: {$intent}
{$langGate}

GUARDRAILS:
- Ein Kunde ist bereits im CRM ausgewählt. **NIE** nach Name/Telefon/Adresse fragen.
- **Keine** Meta-Sätze wie „Ich habe dein Konto analysiert…“.
- Direkt mit der Antwort starten. **Nur wenn** ein **konkretes Pflichtfeld** fehlt: genau **1** kurze Rückfrage am Ende.
- Nichts erfinden. Zeige nur: {$sections}.

EINHEITEN & RUNDUNG:
- Metrisch: °C, m², kW, kWh, kWp, W/K.
- kW/kWp auf 0,1 runden; Flächen ganze m²; Stückzahlen ganzzahlig.

BATTERIE (intent=battery):
- Tageslast ≈ Jahresverbrauch_kWh / 365
- Empfehlung Speicher ≈ 0,6–1,2 × Tageslast (kWh) und ≈ 0,6–1,0 × PV-kWp (kWh)

HEIZLAST (intent=heizlast):
- Falls vorhanden, nutze: brief.norm_outdoor_temp_c, brief.tech.building.heated_area_m2, ggf. brief.calc.*.
- Wenn heated_area_m2 fehlt: **Nur diese** eine Zahl erfragen (m²), sonst rechnen.
- Norm-Außentemperatur: wenn fehlt, konservativ −10 °C annehmen und **als Annahme kennzeichnen**.
- Ausgabeform:
  Kurzfassung: X,X kW
  Schritte (kurz): 2–3 Bulletpoints (Eingaben/Annahmen)
  Nächste Schritte: 1 Zeile (z. B. “Hydraulik prüfen/Detailberechnung …”)

WETTER (intent=weather):
- Nur brief.weather_today verwenden (Temp, gefühlt, Wind, Niederschlag, Zeitstempel).

NORMTEMP (intent=normtemp):
- brief.norm_outdoor_temp_c verwenden; als groben Auslegungswert deklarieren.

ROOF AREA (intent=roofarea):
- brief.roof_area_estimate verwenden: area_m2, source, assumptions nennen.
SYS;

       if ($intent === 'branding') {
            return $core . "\n\n" .
                "BRANDING_ANSWER (raw Markdown, no code fences, no leading spaces):\n" .
                $brandingQA . "\n\n" .
                "RULES FOR THIS RESPONSE:\n" .
                "- Output **exactly** the BRANDING_ANSWER above.\n" .
                "- **Do not** wrap in backticks or code blocks.\n" .
                "- **Do not** add any extra text, emojis, headers, or prefaces.\n";
        }

        // Non-branding: DO NOT include the branding block at all.
        return $core;
    }
}
