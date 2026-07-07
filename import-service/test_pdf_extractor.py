"""Tests der Vektor-PDF-Extraktion (PyMuPDF). Lauf: cd import-service && .venv/bin/pytest -q"""

import os
import tempfile

import fitz

from pdf_extractor import extract_pdf


def _pdf(zeichnen: bool) -> str:
    doc = fitz.open()
    seite = doc.new_page(width=595, height=842)  # A4 in Punkten
    if zeichnen:
        seite.draw_rect(fitz.Rect(100, 100, 300, 250), color=(0, 0, 0), width=1)
        seite.draw_line(fitz.Point(100, 100), fitz.Point(300, 100), color=(1, 0, 0))
        seite.insert_text(fitz.Point(150, 400), "4,00 m")
    f = tempfile.NamedTemporaryFile(delete=False, suffix=".pdf")
    f.close()
    doc.save(f.name)
    doc.close()
    return f.name


def test_vektor_pdf_liefert_entities_ohne_massstab():
    pfad = _pdf(zeichnen=True)
    try:
        r = extract_pdf(pfad)
    finally:
        os.unlink(pfad)
    assert r["pdf_typ"] == "vektor"
    assert r["einheit"] == "punkt"
    assert r["massstab_mm_pro_einheit"] is None  # Maßstab unbekannt → Kalibrierung im Editor
    assert len(r["entities"]) >= 1
    assert r["bbox"] is not None
    assert r["bbox"]["max"]["x"] <= 595 and r["bbox"]["min"]["x"] >= 0  # y-up-Flip in Seitenrahmen
    assert any("4,00" in t["text"] for t in r["texte"])  # eingebetteter Text ohne OCR
    assert r["konfidenz"] == "mittel"


def test_leere_seite_gilt_als_raster():
    pfad = _pdf(zeichnen=False)
    try:
        r = extract_pdf(pfad)
    finally:
        os.unlink(pfad)
    assert r["pdf_typ"] == "raster"
    assert r["entities"] == []
    assert r["bbox"] is None
    assert r["konfidenz"] == "niedrig"
