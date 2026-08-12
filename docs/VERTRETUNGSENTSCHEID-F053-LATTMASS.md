# Vertretungsentscheid F-053 — Lattmaß aus Sparrenlänge und Ziegelbereich

```yaml
entscheid: "F-053 · Lattmass-Teilung"
rolle: "Planner in Vertretung Yamas — von ihm erteilt 12.08.: 'gerade kannst du die
        Verantwortung und Loesung uebernehmen und kannst du mich nur zu dieser vollstaendig
        vertreten'. Die Vertretung ist auf DIESE Frage begrenzt."
grundlage: "Yamas Fachaussage 12.08.: 'die eindecklattung ist abhaengig von dach neigung und
        dach maße und zulaessig ueberlappung der ziegel' · W-23 (BEREIT) · braas_dachziegel_
        datenbank_v14.xlsx, Blatt DB_Produkte"
eintrag_steht_aus: "FORMELSAMMLUNG.md liegt im Scope von A-16 (Kriterium A-16-6, laeuft beim
        Generator). Der Eintrag folgt, sobald §3 die Datei freigibt — H-4."
ampel_vorgesehen: "🟡 mit Geltungsbereich"
```

## Was ich entschieden habe — und der Vorschlag, den ich dabei verworfen habe

**In W-23 habe ich vorgeschlagen:** `n = aufrunden(L / Lattmass_max)`, dann `Lattmaß = L/n`, zulässig
wenn im Bereich. **Diese Fassung ist falsch, und ich verwerfe sie.**

**Gemessen an den sieben Braas-Modellen, Sparrenlängen 1.000–9.000 mm in 10-mm-Schritten (801 Fälle
je Modell):**

```text
Modell             naive Formel liefert einen Wert AUSSERHALB des Bereichs
Rubin 9V           146 von 801   18,2 %
Harzer Pfanne 7    136 von 801   17,0 %
Achat 12V          100 von 801   12,5 %
Rubin 13V          100 von 801   12,5 %
Granat 11V          63 von 801    7,9 %
Topas 13V           55 von 801    6,9 %
Topas 11V           21 von 801    2,6 %
```

> **Meine erste Fassung wäre in bis zu jedem fünften Fall falsch gewesen** — und zwar *leise*: sie
> liefert eine Zahl, die aussieht wie ein Ergebnis. *Bei `Harzer Pfanne 7` und L = 1.000 mm rechnet
> sie `n = 3` und `333,3 mm` — der Ziegel erlaubt aber nur 372–405 mm.*

## Die entschiedene Fassung

```text
F-053 · Lattmass-Teilung

  EINGABE   L        Sparrenlaenge Traufe -> First, IN DER DACHFLAECHE gemessen (mm)
            min,max  Lattmass_min_mm, Lattmass_max_mm des gewaehlten Ziegels
            neigung  Dachneigung (Grad), rdn = Regeldachneigung des Ziegels

  SCHRANKE  neigung >= rdn        sonst: Ziegel NICHT zulaessig, keine Rechnung

  TEILUNG   n_min = aufrunden(L / max)
            n_max = abrunden (L / min)

            n_min <= n_max   ->  TEILBAR.
                                 Jedes ganzzahlige n im Bereich ist zulaessig;
                                 Lattmass = L / n.
                                 n = n_min gibt das GROESSTE Lattmass (wenigste Reihen),
                                 n = n_max das kleinste.

            n_min >  n_max   ->  KEINE gleichmaessige Teilung moeglich.
                                 Die Formel gibt KEINEN Wert zurueck, sondern diesen Fall.

  AUSGABE   Reihenzahl n, Lattmass in mm — ODER die Aussage "nicht gleichmaessig teilbar"
```

**Der Kern der Berichtigung:** *`n_min > n_max` ist **kein Rechenfehler, sondern ein echter Fall**.
Zwischen zwei aufeinanderfolgenden Reihenzahlen liegt eine Lücke — bei `Harzer Pfanne 7` und
L = 1.000 mm ergibt `n = 2` ein Lattmaß von 500 mm (zu groß) und `n = 3` eines von 333 mm (zu klein).
**Es gibt dort keine gleichmäßige Teilung.** Die naive Formel hat diesen Fall in eine falsche Zahl
verwandelt; die entschiedene Fassung benennt ihn.*

## Der Geltungsbereich — und deshalb 🟡, nicht 🟢

```text
F-053 rechnet die REGELFLAECHE: gleichmaessige Reihen zwischen Traufe und First.

NICHT ERFASST — und jede dieser Groessen kann das Ergebnis verschieben:
  Traufreihe        eigener Ueberstand, eigenes Mass; die erste Latte liegt nicht
                    im Regelabstand
  Firstanschluss    Firstabstand nach Ziegel- und Firstsystem
  Ortgang/Grat      seitliche Anschluesse
  Restausgleich     genau der Fall n_min > n_max: die Praxis verteilt die Restlaenge
                    auf Traufe und First. WIE, ist Handwerkspraxis und steht nicht
                    in der Quelle.
  Verschiebespiel   liegt als Zahl vor (= max - min, sechs von sechs geprueft), aber
                    seine Anwendung auf die Randreihen ist NICHT belegt.

-> F-053 ist eine VORBEMESSUNG der Reihenteilung. Kein Verlegeplan, keine
   Ausfuehrungsangabe, kein Ersatz fuer die Lattung nach Herstellervorgabe.
```

> **Warum 🟡 und nicht 🟢:** *dieselbe Begründung wie bei N-003 — nicht die Rechenqualität ist das
> Problem, sondern die **Vollständigkeit des Geltungsbereichs**. Die Regelfläche rechnet sie richtig,
> nachgemessen an 5.607 Fällen. Aber ein Dach besteht nicht nur aus Regelfläche, und was an Traufe
> und First passiert, steht in keiner mir zugänglichen Quelle.*

## Was ich in Vertretung entschieden habe, und was ich NICHT entschieden habe

```text
ENTSCHIEDEN   die Teilungsformel samt der Existenzpruefung (n_min <= n_max)
              die Neigungsschranke als Vorbedingung
              die Ampel 🟡 mit dem Geltungsbereich oben
              die Verwerfung meiner eigenen ersten Fassung

NICHT ENTSCHIEDEN, und das bleibt bei Yama:
              WIE die Restlaenge auf Traufe und First verteilt wird, wenn keine
              gleichmaessige Teilung existiert. Das ist Handwerkspraxis; ich kenne
              die Regel nicht und erfinde sie nicht. Bis dahin gibt F-053 in diesem
              Fall KEINEN Wert zurueck — das ist die ehrliche Ausgabe.

              Welches n gewaehlt wird, wenn mehrere zulaessig sind. Fachlich spricht
              die Praxis fuer das GROESSTE Lattmass (wenigste Reihen, weniger Material),
              aber bei geringer Neigung fuer das KLEINSTE (mehr Ueberdeckung, mehr
              Regensicherheit). Genau hier wirkt Yamas "abhaengig von der Dachneigung"
              ein zweites Mal — und diese Abhaengigkeit ist in der Quelle nicht bezifert.
```

## Warum ich das übernehmen konnte, ohne Fachwissen zu erfinden

*Yamas Satz nennt die drei Größen. Die Quelle liefert zwei davon als Zahlen (Bereich, Neigungs-
schranke) und die dritte als Maß (Verschiebespiel). **Was ich beigetragen habe, ist die Arithmetik —
und die ist prüfbar, nicht fachlich:*** *dass zwischen zwei Reihenzahlen eine Lücke liegen kann, ist
Teilbarkeit, kein Dachdeckerwissen. Genau deshalb konnte ich meinen eigenen Vorschlag widerlegen,
**bevor** er Formel wurde.*

> **Und der Fehler war meiner:** *ich habe die naive Fassung in W-23 als Vorschlag eingetragen, ohne
> sie gegen die Daten zu rechnen. Sie sah richtig aus und ging bei den ersten vier Sparrenlängen auf,
> die ich probiert hatte — 28 von 28. Erst die **gezielte Suche nach Fehlschlägen** hat die Lücke
> gezeigt. Vier Stichproben, die alle grün sind, sind kein Beleg; sie sind vier Stichproben.*

```yaml
naechster_schritt: "F-053 in FORMELSAMMLUNG.md eintragen, sobald A-16 die Datei freigibt (§3/H-4).
        Danach W-23s Kriterium W-23-2 auf die ENTSCHIEDENE Fassung umstellen — dort steht heute
        noch die verworfene."
was_yama_noch_gehoert: "die Restausgleich-Regel und die Wahl des n bei mehreren Moeglichkeiten.
        Beides ist benannt, keines erfunden."
belegzahlen: "7 Modelle x 801 Sparrenlaengen = 5.607 gerechnete Faelle. Fehlschlagquote der
        verworfenen Fassung 2,6 % bis 18,2 %."
```
