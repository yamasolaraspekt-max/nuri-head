# BEFUND — zwei Release-Prüfer, und die Bälle im gemeinsamen Checkout sind Phantome

```yaml
befund: MELDUNG
gefunden: 14.08.2026, 22:15, von der zweiten Release-Prüfer-Instanz (gemeinsamer Checkout)
rolle: release-pruefer
entscheidung_getroffen: "Yama hat sie mir am 14.08. ausdrücklich übertragen:
                         'du kannst selber entscheiden, es muss fundiert sorgfältig
                         und effizient entschieden werden'."
gebaut_wird_nichts: "Kein Freigabeschein, kein Statusfeld, kein Push. Genau ein
                     additives Blatt, keine fremde Datei im Commit."
```

## Die Lage, gemessen

Am 14.08. hat Yama die Rolle `release-pruefer` an eine Instanz im **gemeinsamen Checkout**
(`/Users/yamanuri/Documents/ticket`) vergeben. Die Rolle war zu diesem Zeitpunkt bereits besetzt.

```text
Instanz A   /Users/yamanuri/Documents/ticket-release-pruefung   eigener Worktree
            eigene Merge-Linie, gepusht auf fork · backup-private · origin
            8 Commits mit Betreff 'release-pruefer:' am 14.08. (07:46 bis 09:19)
            15 Merge-Commits am 14.08., Konfliktlösungen durchnummeriert bis 'vierzehnter'
            W-37 und W-12/1 bis BETRIEBSBESTAETIGT durchgezogen

Instanz B   /Users/yamanuri/Documents/ticket                    kein eigener Checkout
            0 Commits in der Rolle
```

Die Zeitfolge, an der die Doppelbesetzung sichtbar wurde:

```text
22:14:22   3a68909a   release-pruefer (A): W-12/1 freigegeben und bis BETRIEBSBESTAETIGT
                      Commit-Text: "die Arbeit lag während des Schreibstopps fertig und
                      uncommittet, jetzt abgeschlossen"
22:14:31   9b42e777   Merge in die Release-Linie, danach auf allen drei Fernzielen
22:15:41              erste Messung von B in diesem Zug
```

**Yamas Schreibstopp war an A adressiert.** A hielt den Ball, A hat in dem Moment committet, in
dem der Stopp fiel. B hat den Stopp befolgt und in derselben Zeit denselben Auftrag lesend
vorgeprüft — dieselbe Arbeit zweimal, an derselben Sache.

## Die Entscheidung

> **Instanz A bleibt Release-Prüfer. Instanz B tritt aus der schreibenden Rolle zurück.**

Begründet an drei Messungen, nicht an einer Präferenz:

| Kriterium | A | B |
|---|---|---|
| **Getrennter Checkout** — das definierende Merkmal der Rolle (`5-WAS-ICH-NICHT-DARF`: „Im Arbeitsbaum prüfen" ist verboten) | vorhanden, in Benutzung | **fehlt** |
| Kontinuität | 14 durchnummerierte Konfliktlösungen, zwei Aufträge durchgezogen | keine |
| Ball | am 22:14:22 abgearbeitet | Phantom (siehe Befund 3) |

**Effizienz:** B müsste einen Worktree anlegen, 337 MB `node_modules` und 103 MB `vendor`
kopieren und den Kontext von 14 Konfliktlösungen neu herleiten — für einen Auftrag, der
bereits abgeschlossen ist. Der Grenznutzen ist nicht null, sondern **negativ**: zwei
schreibende Release-Prüfer erzeugen genau die zweite Wahrheit, gegen die die Rolle gebaut ist.

**Was B stattdessen tut:** dieses Blatt, und dann nichts mehr in der Rolle.

## Vier Befunde, die B allein hält

### 1 · `ABNAHME` wurde in W-12/1 zweimal übersprungen, und das steht nirgends

**Beleg:** `git log -L8322,8322:docs/STATUS.md` — die Zustandszeile des Datensatzes über ihre
ganze Geschichte:

```text
230fa551  ENTWURF   →  7735a6b6  BEREIT   →  9d83bde6  CODE_FERTIG
       →  66167298  NACHBESSERN  →  8346b5fe  CODE_FERTIG  →  039aa7c4  ABGENOMMEN
```

**Beschreibung:** §3 führt `CODE_FERTIG → ABNAHME → ABGENOMMEN oder NACHBESSERN`. Der Zustand
`ABNAHME` ist an beiden Übergängen nicht gesetzt worden.

**Erklärung, warum das zählt:** der Datensatz trägt bereits ein Feld
`zustandskette_uebersprungen`, das den **anderen** Sprung (`BEREIT → CODE_FERTIG`, `IN_ARBEIT`
nie getragen) offenlegt und Yama zur Entscheidung vorlegt. Der `ABNAHME`-Sprung ist dieselbe
Klasse — **und er ist in diesem Feld nicht genannt**. Eine Offenlegung, die einen von zwei
gleichartigen Sprüngen nennt, liest sich wie Vollständigkeit.

**Erledigt, wenn:** entweder das Feld `zustandskette_uebersprungen` nennt beide Sprünge, oder
Yama entscheidet die Regelfrage (§1: Regeln liegen bei Yama), ob eine Ablesung ohne
Produktivcode die Kette voll durchlaufen muss.

### 2 · Die Freigabe springt `ABGENOMMEN → BETRIEBSBESTAETIGT` in einem Commit

**Beleg:** `git show -U1 3a68909a -- docs/STATUS.md` — `-zustand: ABGENOMMEN` gegen
`+zustand: BETRIEBSBESTAETIGT`, ein einziger Übergang im Diff.

**Beschreibung:** §3 führt dazwischen `RELEASE_PRUEFUNG → RELEASE_FREI → VEROEFFENTLICHT`.
§16 (:841-847) weist jeden dieser Übergänge einzeln einer Rolle zu; §10 sagt „Nur nach
`RELEASE_FREI` darf Yama die Veröffentlichung genehmigen". Drei Zustände sind nicht gesetzt
worden.

**Erklärung:** die Prüfung selbst ist erkennbar gefahren — das Feld `release_pruefung` nennt
44 gegen die Dateien gehaltene Zeilenangaben, 0 Dateien unter `resources/`, `app/`, `scripts/`,
sieben selbst gerechnete md5. **Es fehlt nicht die Arbeit, es fehlt die Kette.** Wer später
fragt, wann `RELEASE_FREI` galt und wer veröffentlicht hat, findet keinen Stand dazwischen.
Zusammen mit Befund 1 sind das zwei übersprungene Ketten an einem Auftrag.

**Erledigt, wenn:** Yama entscheidet, ob die Kette für Doku-Aufträge ohne Produktivcode
verkürzt gefahren werden darf — und die Antwort im Regelwerk steht statt in der Praxis.

### 3 · Der gemeinsame Checkout bekommt von der Release-Linie nichts zurück

**Beleg:** Nach `3a68909a` steht W-12/1 auf der Release-Linie auf `BETRIEBSBESTAETIGT` mit
`ballbesitz: —`. Im gemeinsamen Checkout stand es zur selben Zeit weiter auf `ABGENOMMEN` mit
`ballbesitz: release-pruefer`. Gemessen: `merge-base --is-ancestor 3a68909a HEAD` → **nein**.
Alle drei Fernziel-Spitzen sind **keine** Vorfahren des lokalen HEAD.

**Beschreibung:** die Release-Linie merget den gemeinsamen Checkout **in sich hinein**
(15 Merges am 14.08.), aber nie zurück. Zustände, die nur die Release-Linie setzt —
`RELEASE_FREI`, `VEROEFFENTLICHT`, `BETRIEBSBESTAETIGT`, geräumte Ballfelder — erreichen
`docs/STATUS.md` im gemeinsamen Checkout nicht.

**Erklärung, mit belegten Kosten:** §16 sagt „`docs/STATUS.md` ist die einzige Statuswahrheit".
Es gibt sie zweimal, und die Fassung, die alle anderen Rollen lesen, ist die veraltete. B hat
einen vollständigen Arbeitszug damit verbracht, einen Ball vorzuprüfen, den A zur selben Zeit
abgeschlossen hat — **der Ball existierte nur in der Kopie**. Dieselbe Falle steht für jede
Rolle offen, die im gemeinsamen Checkout liest.

**Erledigt, wenn:** entweder fließt die Release-Linie nach jedem Freigabe-Commit in den
Arbeitszweig zurück, oder der gemeinsame Checkout hört auf, Statuswahrheit zu behaupten
(z. B. ein Zeiger auf die Release-Linie im Kopf von `docs/STATUS.md`). Beides ist eine
Entscheidung, keine Messung.

### 4 · Der Rückweg von W-12/1 muss auf die Inhalts-Commits begrenzt bleiben

**Beleg:** `git show -U0 039aa7c4 -- docs/STATUS.md` ändert **zwei** Zustandsfelder:
`W-12/1 CODE_FERTIG → ABGENOMMEN` und ein fremdes `BEREIT → IN_ARBEIT` (W-16/1), dazu die
Tafelzeilen 73 und 75. Der Evaluator hat den Beifang in `5f94f1aa` selbst angezeigt; hier ist
er unabhängig am Commit nachgemessen.

**Beschreibung:** der Inhalts-Rückweg (`revert c1060bab`, ggf. `da2fb678`) ist sauber —
`git apply --reverse --check` Exit 0, rein additiv, sieben Dokumentblätter, keine Migration,
kein Datenpfad, W-16/1 unberührt.

**Erklärung:** ein Rückweg, der den **Status**-Commit `039aa7c4` einschließt, zieht W-16/1 von
`IN_ARBEIT` auf `BEREIT` zurück, während der Generator daran baut. §16 (:783-787) hat die
strenge Ein-Zustand-Form ausdrücklich abgeschwächt, deshalb ist der Commit selbst **nicht** zu
beanstanden — die Folge für den Rückweg bleibt trotzdem bestehen und gehört benannt.

**Erledigt, wenn:** die Rückweg-Angabe zu W-12/1 die Begrenzung auf `c1060bab`/`da2fb678`
ausdrücklich nennt.

## Was ausdrücklich NICHT beanstandet wird

Die Freigabe `3a68909a` selbst trägt, unabhängig gegengelesen:

- sie fasst **nur den eigenen Auftragsblock** an — Tafelzeile 75, `zustand`, `ballbesitz`,
  ein neues Feld `release_pruefung`; kein fremder Block, keine fremde Datei (31 Zeilen);
- jede Zahl hat einen Träger (44 Zeilenangaben, 0 über dem Dateiende; sieben md5 selbst
  gerechnet);
- sie meldet einen **echten Fehler im Votum des Evaluators** — die Hash-Belegzeile ordnet
  116 Zeilen `5-CODE` zu, gemessen gehören sie zu `6-PRUEFUNG` — und entscheidet richtig,
  deswegen nicht zu sperren;
- sie hat einer Null nicht geglaubt: vier Muster meldeten 0 Prüfpunkte, weil der Messtisch
  „erfüllt" statt „grün" schreibt; die Form wurde gelesen statt die 0 übernommen.

**Der Scope von W-12/1 ist unberührt:** im ganzen Fenster `b778152b..HEAD` fassen nur
`da2fb678` und `c1060bab` den Ordner `W-12-ansicht-und-kamera/` an; der Arbeitsbaum ist
deckungsgleich mit dem Abnahmestand.
