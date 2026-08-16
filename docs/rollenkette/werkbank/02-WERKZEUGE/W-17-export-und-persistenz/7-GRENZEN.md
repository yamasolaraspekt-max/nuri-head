# W-17 · Export und Speichern — GRENZEN

## Die Snapshot-Naht ist auf dem Server fertig und in der Insel nicht angeschlossen

**Das ist die tragende Grenze dieses Werkzeugs**, und sie ist von einem eigenen Wächter
festgehalten (`snapshotFlaecheEhrlich.test.ts`, AUF-55, 110 Z.):

```text
GEBAUT auf dem Server   POST /objekt/{objekt}/snapshots     anlegen
                        GET  /objekt/{objekt}/snapshots     listen
                        StelleSnapshotWieder.php  83 Z.     wiederherstellen
                        objekt.blade.php setzt data-snapshots-url

FEHLT in der Insel      main.tsx liest speichernUrl, rechte, projekte, paketeUrl
                        — die Snapshot-Adresse NICHT
```

> ***Der Rückweg existiert vollständig und ist für den Anwender unerreichbar.*** *Der Wächter sagt
> auch, warum das schlimmer ist als eine leere Fläche:* **„sie wird beim nächsten Mal neu erfunden,
> weil niemand weiß, dass sie schon da ist."**
>
> **Das ist keine Lücke im Sinne von „unfertig", sondern eine gezogene und nicht angeschlossene
> Leitung** — *und der Unterschied entscheidet, was der nächste Auftrag kostet.*

## Der PNG-Export ist keine Sicherung, und das steht nirgends

```text
app/HausplanerApp.tsx:664  exportPng()   toDataURL, download 'grundriss.png'
                           kein Server, keine Revision, kein Rueckweg
                           KEIN Waechter
```

> **Was heruntergeladen ist, kennt der Planer nicht mehr.** *Ein Bild trägt keine Wände — es lässt
> sich ansehen und nicht zurückladen.* **Nirgends in der Oberfläche steht das**, und die Nähe zum
> Speichern-Knopf legt das Gegenteil nahe.
>
> *Ob dort ein Hinweis hingehört, ist eine Produktfrage und in diesem Blatt nicht entschieden.*

## Beim Konflikt gibt es nur einen Weg: neu laden

**`speicherAnzeige.ts:56-60`** — *Text und Knopftitel bieten ausschließlich das an.*

```text
„Konflikt: Plan wurde von anderer Seite geändert (Revision N) — Seite neu laden"
Titel: „Erst neu laden — sonst würde der fremde Stand überschrieben."
```

> ***Kein Zusammenführen, kein Nebeneinanderlegen, kein „meinen Stand behalten".*** *Der Anwender
> verliert seine ungespeicherte Arbeit, wenn er neu lädt* — **und der Knopf ist gesperrt, damit er
> sie nicht stattdessen über den fremden Stand schreibt.**
>
> **Die Sperre ist die richtige Entscheidung und trotzdem eine Grenze:** *sie schützt die fremde
> Arbeit auf Kosten der eigenen.* *Was es nicht gibt, ist ein dritter Weg.*

## Gespeichert wird immer die GANZE Szene

*Kein Diff, keine Teilspeicherung* (`2-FUNKTION`). **Das ist der Grund, warum die Prüfsumme über die
ganze Szene läuft und die Revision als Vergleich genügt** — *eine Differenzkette hätte Glieder, und
ein verlorenes wäre nicht bemerkbar.*

**Der Preis steht daneben:** *`SpeichereHausplanerDokumentRequest.php:61` prüft die Größe, weil eine
wachsende Szene bei jedem Speichern vollständig über die Leitung geht.*

## Was ich in diesem Baum NICHT prüfen konnte

```text
tests/Feature/Hausplaner/HausplanerSpeichernNutzlastTest.php   15 Zusagen
tests/Feature/Hausplaner/SnapshotRueckwegVersionTest.php        4 Zusagen
  -> vendor/ fehlt im Rollenbaum; composer install je Baum ist NICHT entschieden
```

> ***Die Hälfte des Gegenstands ist damit gelesen und nicht gefahren.*** *Ich habe die Zusagen
> namentlich aufgeführt* (`6-PRUEFUNG`) *und die Lücke gemeldet, statt sie durch eine eigenmächtige
> Installation zu schließen.* **Eine Ablesung, die verschweigt, was sie nicht messen konnte, ist
> schlechter als eine, die es benennt.**

## Nachbarschaft — nur abgegrenzt

```text
W-33 / W-36   paketSpeichern, schienenSpeicher — andere Gegenstaende,
              trotz „Speicher" im Namen (H-9, s. 5-CODE)
W-05          die Raumflaeche — NICHT snapshotFlaecheEhrlich, das gehoert hierher
alle          die Registerzeile nennt als Nachbarn „alle": jedes Werkzeug, das
              die Szene aendert, endet in diesem Speicherweg
```
