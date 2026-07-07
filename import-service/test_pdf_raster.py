"""Tests der Raster-PDF-Rendering (PyMuPDF). Lauf: cd import-service && .venv/bin/pytest -q"""

import os
import tempfile

import fitz
import pytest

from pdf_raster import PdfGeschuetzt, rasterize_pdf


def _pdf(width: float, height: float, seiten: int = 1, verschluesselt: bool = False) -> str:
    doc = fitz.open()
    for _ in range(seiten):
        doc.new_page(width=width, height=height)
    f = tempfile.NamedTemporaryFile(delete=False, suffix=".pdf")
    f.close()
    if verschluesselt:
        doc.save(f.name, encryption=fitz.PDF_ENCRYPT_AES_256, owner_pw="o", user_pw="u")
    else:
        doc.save(f.name)
    doc.close()
    return f.name


def test_rasterize_liefert_png_und_metadaten():
    pfad = _pdf(595, 842, seiten=2)  # A4, 2 Seiten
    try:
        r = rasterize_pdf(pfad)
    finally:
        os.remove(pfad)
    assert r["png"][:8] == b"\x89PNG\r\n\x1a\n"  # PNG-Signatur
    assert r["effektive_dpi"] == 200
    assert r["quelle_seite"] == 1 and r["seiten_gesamt"] == 2
    assert r["breite"] > 1000 and r["hoehe"] > 1000


def test_dpi_deckel_reduziert_bei_grosser_seite():
    pfad = _pdf(3000, 3000)  # 3000pt → bei 200 DPI 8333 px > 6000 → DPI gesenkt
    try:
        r = rasterize_pdf(pfad)
    finally:
        os.remove(pfad)
    assert r["effektive_dpi"] < 200
    assert max(r["breite"], r["hoehe"]) <= 6000


def test_passwortgeschuetztes_pdf_klare_meldung():
    pfad = _pdf(595, 842, verschluesselt=True)
    try:
        with pytest.raises(PdfGeschuetzt):
            rasterize_pdf(pfad)
    finally:
        os.remove(pfad)
