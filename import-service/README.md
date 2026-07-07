# import-service

Python-FastAPI-Microservice für den Plan-Import von `wberechnung`. **DXF-Rohextraktion** (ezdxf) +
**PDF** (PyMuPDF): Vektor-Extraktion und Raster-Rendering. Reines Backend; das Laravel-Frontend bleibt
Alpine/SVG. Ist `IMPORT_SERVICE_URL` in Laravel leer, wird der Service **nie** aufgerufen (graceful aus).

## Setup (bare uvicorn, kein Docker nötig)

```bash
cd import-service
python3 -m venv .venv
.venv/bin/pip install -r requirements.txt
.venv/bin/uvicorn main:app --port 8001
```

Dann in Laravels `.env`: `IMPORT_SERVICE_URL=http://127.0.0.1:8001`

## Endpunkte

- `GET /health` → `{status, ezdxf_version, pymupdf_version, ocr_verfuegbar}`
- `POST /extract/dxf` (multipart `datei`) → Kandidaten-Geometrie-JSON (mm): `einheit, massstab_mm_pro_einheit,
  bbox, layers[], entities[], dimensions[], texte[], konfidenz`.
- `POST /extract/pdf` (multipart `datei`) → wie oben, Koordinaten in PDF-Punkten, plus `pdf_typ=vektor|raster`,
  `texte[]` (eingebettet, ohne OCR). Maßstab unbekannt → Kalibrierung im Editor (A-3c).
- `POST /rasterize/pdf` (multipart `datei`) → **PNG** (`image/png`) der flächengrößten Seite @200 DPI
  (6000-px-Deckel, DPI sonst proportional gesenkt). Metadaten in Headern: `X-Breite, X-Hoehe, X-Quelle-Seite,
  X-Seiten-Gesamt, X-Effektive-DPI`. Passwortgeschützt → `422` mit Klartextmeldung.
- `POST /ocr` (multipart `datei`, Bild) → `{texte:[{text, position, conf}], ocr_verfuegbar}` (tesseract `deu`,
  PSM 11, conf≥60). Fehlt tesseract → `{texte:[], ocr_verfuegbar:false}` (kein Fehler).

## OCR (tesseract) — optional, graceful-off

OCR liest Maßstabs-/Bemaßungstext aus Bild/Raster-PDF und macht daraus **Vorschläge**. **Ohne tesseract ist OCR
still deaktiviert** — der Import (Underlay, manuelle Kalibrierung) funktioniert vollständig weiter.

```bash
# lokal (macOS/Herd):
brew install tesseract tesseract-lang        # tesseract-lang liefert 'deu' (Pflicht)
# Server (Debian/Ubuntu):
sudo apt install tesseract-ocr tesseract-ocr-deu
```

## Tests

```bash
cd import-service && .venv/bin/pytest -q
```

## Scope / offen

Rohextraktion + Rasterung + OCR-Notation (optional, graceful-off) — **keine** Wand-Rekonstruktion (Editor),
**kein** DWG/ODA (optionales Add-on), **kein** Raster-ML.
