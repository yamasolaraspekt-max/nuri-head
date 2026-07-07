"""Raster-PDF → PNG-Rendering mit PyMuPDF (A-3d-2 Stufe 2a).

Rendert die flächengrößte Seite eines (Raster-)PDFs zu einem PNG als Underlay-Quelle.
Ziel-DPI 200 (Balance Underlay/OCR); überschreitet die Seite max_kante_px, wird die DPI
proportional reduziert (effektive DPI wird zurückgegeben). Passwortgeschützte PDFs → klarer
Fehler (kein Stacktrace an den Nutzer). KEIN OCR (das ist Stufe 2b).
"""

import fitz

ZIEL_DPI = 200
MAX_KANTE_PX = 6000


class PdfGeschuetzt(Exception):
    """PDF ist passwortgeschützt — klare Nutzermeldung statt Stacktrace."""


def _groesste_seite(doc):
    best_idx, best = 0, doc[0]
    for i, seite in enumerate(doc):
        if seite.rect.width * seite.rect.height > best.rect.width * best.rect.height:
            best_idx, best = i, seite
    return best_idx, best


def rasterize_pdf(pfad: str, ziel_dpi: int = ZIEL_DPI, max_kante_px: int = MAX_KANTE_PX) -> dict:
    doc = fitz.open(pfad)
    try:
        if doc.needs_pass:
            raise PdfGeschuetzt('PDF ist passwortgeschützt — bitte entsperrt hochladen.')

        seiten_gesamt = doc.page_count
        seite_idx, seite = _groesste_seite(doc)

        # DPI-Deckel: max Kantenlänge in Pixeln begrenzen, DPI sonst proportional senken.
        max_pt = max(seite.rect.width, seite.rect.height) or 1.0
        px_bei_ziel = max_pt / 72.0 * ziel_dpi
        dpi = ziel_dpi if px_bei_ziel <= max_kante_px else max(72, int(ziel_dpi * max_kante_px / px_bei_ziel))

        pix = seite.get_pixmap(dpi=dpi)

        return {
            "png": pix.tobytes("png"),
            "breite": pix.width,
            "hoehe": pix.height,
            "quelle_seite": seite_idx + 1,
            "seiten_gesamt": seiten_gesamt,
            "effektive_dpi": dpi,
        }
    finally:
        doc.close()
