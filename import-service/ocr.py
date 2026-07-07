"""OCR eines Underlay-Bildes mit tesseract/pytesseract (A-3d-2 Stufe 2b).

Läuft auf dem BEREITS gerenderten Underlay-Bild (Original bei Bild-Upload, abgeleitetes PNG bei
Raster-PDF) — kein zweites Rendern (Ein-Pass-Regel). Liefert erkannte Wörter mit Bounding-Box-Mitte
und Konfidenz. Nur Vorschlag-Rohdaten; Maßstab setzt weiterhin ausschließlich der Nutzer.

Zwei-Ebenen-graceful-off: fehlt pytesseract (Import) ODER das tesseract-Binary (Server), liefert
`ocr_bild` `{texte: [], ocr_verfuegbar: False}` (kein Fehler) — der Underlay-Pfad bleibt voll nutzbar.
"""

import io

PSM = 11          # sparse text: verstreute Maß-/Beschriftungstexte auf Plänen (keine Absätze)
MIN_CONF = 60     # tesseract-Konfidenz 0..100; darunter verwerfen statt raten (tunebar)
SPRACHE = "deu"

try:
    import pytesseract
    from PIL import Image

    _IMPORT_OK = True
except Exception:  # noqa: BLE001 — pytesseract/Pillow fehlt → OCR still aus
    _IMPORT_OK = False


def verfuegbar() -> bool:
    """OCR nutzbar? Prüft Import (Ebene a) UND tesseract-Binary (Ebene b)."""
    if not _IMPORT_OK:
        return False
    try:
        pytesseract.get_tesseract_version()

        return True
    except Exception:  # noqa: BLE001 — Binary fehlt/kaputt
        return False


def ocr_bild(png_bytes: bytes, lang: str = SPRACHE, psm: int = PSM, min_conf: int = MIN_CONF) -> dict:
    if not verfuegbar():
        return {"texte": [], "ocr_verfuegbar": False}

    try:
        img = Image.open(io.BytesIO(png_bytes))
        data = pytesseract.image_to_data(img, lang=lang, config=f"--psm {psm}", output_type=pytesseract.Output.DICT)
    except Exception:  # noqa: BLE001 — kaputtes Bild → leer, nie Fehler in die Kette
        return {"texte": [], "ocr_verfuegbar": True}

    texte = []
    for i in range(len(data["text"])):
        wort = (data["text"][i] or "").strip()
        try:
            conf = float(data["conf"][i])
        except (ValueError, TypeError):
            conf = -1.0
        if not wort or conf < min_conf:
            continue
        x = data["left"][i] + data["width"][i] / 2
        y = data["top"][i] + data["height"][i] / 2
        texte.append({"layer": "ocr", "text": wort, "position": {"x": round(x, 1), "y": round(y, 1)}, "conf": round(conf, 1)})

    return {"texte": texte, "ocr_verfuegbar": True}
