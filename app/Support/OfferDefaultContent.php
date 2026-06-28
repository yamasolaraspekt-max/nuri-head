<?php

namespace App\Support;

class OfferDefaultContent
{
    public static function defaultAgb(): string
    {
        return <<<HTML
<div class="space-y-5 text-[12px] leading-relaxed text-slate-800">
    <div>
        <div class="font-black text-[16px] mb-1">Transparenz &amp; Fairness - Unsere Vereinbarung</div>
        <p>
            Für die Umsetzung Ihres Projekts gelten unsere Allgemeinen Geschäftsbedingungen (AGB)
            in der jeweils aktuellen Fassung.
        </p>
    </div>

    <div>
        <div class="font-black text-[16px] mb-1">Partnerschaftlich zum Erfolg</div>
        <p>
            Wir legen großen Wert auf eine offene, kooperative und lösungsorientierte Zusammenarbeit.
        </p>
    </div>

    <div class="pt-3">
        <p>
            Dieses Angebot ist freibleibend. Irrtümer und technische Änderungen bleiben vorbehalten.
        </p>
    </div>

    <div>
        <p>
            Ich nehme das Angebot an und bestelle hiermit verbindlich die im Angebot aufgeführten
            Leistungen und Komponenten.
        </p>
    </div>

    <div class="font-black">
        Evtl. sind einige Positionen in Ihrem Angebot als optional aufgeführt.
        Bitte kennzeichnen Sie diese als gewünscht oder als nicht notwendig. Vielen Dank!
    </div>
</div>
HTML;
    }
}