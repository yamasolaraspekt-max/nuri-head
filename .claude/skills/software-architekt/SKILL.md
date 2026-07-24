---
name: software-architekt
description: Code-Linse Software-Architektur für ticket-CRM + Hausplaner (Schichten, eine Wahrheit/SSOT, additive Erweiterung, Reuse vor Neu, Integration/Merge). Laden bei Architektur-, Integrations- und Schnittstellen-Fragen.
---

# software-architekt

## Ziel
Struktur-Entscheidungen so treffen, dass es EINE Wahrheit je Sachverhalt gibt, Erweiterungen additiv sind und
Bestand (LIVE-CRM, 3000 Kunden) nie bricht.

## Prüf-Linse
- **Eine Wahrheit / SSOT.** Kein zweiter Ort, der denselben abgeleiteten Wert erneut rechnet (Fälligkeit,
  Umsatz→`invoices`, Dach-Placement-Anker, Wand-Ecken). Bei Konflikt: kanonische Fassung wählen, zweite stilllegen.
- **Schichten sauber.** Reine Geometrie/Domäne (testbar, kein three/React) getrennt vom dünnen Renderer.
  Fachbedeutung nicht verwässern (Ticket-Status ≠ Objektstatus → Adapter).
- **Additiv & rückwärtskompatibel.** Neue Spalten nullable/Default; neue Sammlung NEBEN bestehender
  (`roofs`/`ceilings`-Muster). Zod-Änderung ⇒ Schema regenerieren. Kein 422, kein Bestandsbruch.
- **Reuse vor Neu.** Vor einer neuen Klasse/Route/Migration: `git log`/`grep` — existiert es schon? (Lehre:
  `TopologieGate` fast doppelt gebaut). Blinden Service nicht „reparieren", wenn eine Grenze ihn bewusst meidet.
- **Integration/Merge.** Divergente Modelle additiv vereinen (3-Wege-Merge, gemeinsamer Vorfahr); Bundle nie
  mergen (neu bauen); kanonische Datei je Kollision benennen; Vereinigung ALLER Tests grün, nicht Teilmenge.

## Rote Flaggen
- Parallele zweite Implementierung/Berechnung. „Übergangsweise" doppelte Wahrheit. Auto-Merge ohne Kollisions-Map.
- Neue UI-/Status-/Aufgaben-Logik, die es im Ticket schon gibt.

## Grenze
Tor 2 (main-Merge/Deploy) ist Yama-Entscheidung, keine Architektur-Automatik.
