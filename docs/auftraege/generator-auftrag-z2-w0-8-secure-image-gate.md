# Z2-W0-8 · `secure.image` und Geschwister: Recht + Bindung statt bloßem `auth`

```yaml
zustand: ENTWURF
welle: 0 (strukturell; unter der Rechte-Entscheidung vom 21.08. heute ohne interne Wirkung — gebaut für den Tag, an dem der Schalter fällt, und für den Fall externer/fremder Sessions)
basis_sha: 114b98f6
herkunft: Befund A-6 (docs/backlog/inventur-2026-08-21-z2-folge.md), bestätigt durch Messung 21.08.
spur: A — Autorisierung, Kundendaten (Fotos/Dokumente)
baut: generator (Agent backend-entwickler)
nimmt_ab: evaluator — nie der Bauende
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
```

## Ziel
`secure.image` (`/secure-image/id/{id}`), `secure.image.byFilename`, `image/secure/{id}` verlangen
`permission:Customer,read` und nutzen Route-Model-Binding; `secureDownloadScreenshot` prüft Auth
**vor** dem Laden. Objektbindung auf Lead-Ebene ist **ausdrücklich Nicht-Ziel** (kein Lead-Ownership-
Modell im Repo — und unter „alle Rechte für alle" wäre es Yamas Entscheidung zuwider).

## Ist-Beleg
`routes/web.php:1447` Gruppe `['web','auth']`, `:1463-1464`; `ImageController:770-785` `Image::findOrFail($id)`;
`:753-767` findOrFail VOR `auth()->check()`; `:787-819` nur `auth()->check()`. `images.customer_id` →
`new_leads` vorhanden, ungenutzt. 7 Produktiv-Erzeuger. Pfad-Injektion grün.

## Scope · Dateien
`routes/web.php` (drei Routen: `->middleware('permission:Customer,read')`, `whereNumber`), `ImageController`
(Binding `Image $image`; Reihenfolge auth→load), Test `tests/Feature/Security/SecureImageGateTest.php`:
Schalter false: ohne Customer → 403, mit → 200; Schalter true → 200; nicht existierende ID → 404.
**Nicht-Ziele:** keine Lead-Ownership-Regel; keine Änderung der 7 Erzeuger; keine Upload-Änderung
(A-9 `max:` ist eigener S-Posten).

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: `route:list --name=secure.image` zeigt permission-Middleware auf allen drei Routen | Gate | *n.U.* | route:list |
| B: Schalter false: ohne Customer 403, mit 200; Schalter true: 200 | Test | *n.U.* | Testnamen |
| C: `secureDownloadScreenshot` lädt erst nach Auth (Zeilenreihenfolge, diff) | Reihenfolge | *n.U.* | Zitat |
| D: 7 Erzeuger unverändert (grep-Zählung gleich) | Grenze | *n.U.* | grep |

**P1-Kriterium B ist vor dem Bau wirksam rot** (Schalter false: ohne Customer → 200 heute).

## Rückweg
Ein Commit, zurückdrehbar.
