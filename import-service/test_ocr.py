"""Tests des OCR-Moduls (tesseract/pytesseract, deu). Lauf: cd import-service && .venv/bin/pytest -q"""

import fitz

import ocr


def _png(texte, w=595, h=842) -> bytes:
    """Rendert Texte scharf zu einem 200-DPI-PNG (wie das Underlay)."""
    d = fitz.open()
    p = d.new_page(width=w, height=h)
    for (x, y, s) in texte:
        p.insert_text(fitz.Point(x, y), s, fontsize=40)
    png = p.get_pixmap(dpi=200).tobytes("png")
    d.close()

    return png


def test_ocr_happy_path_deu():
    r = ocr.ocr_bild(_png([(60, 100, "M 1:50"), (120, 300, "4,00")]))
    assert r["ocr_verfuegbar"] is True
    texte = [t["text"] for t in r["texte"]]
    assert any("1:50" in t for t in texte)
    assert all("position" in t and "conf" in t for t in r["texte"])


def test_ocr_liest_umlaute_deu():
    r = ocr.ocr_bild(_png([(60, 120, "Küche")]))
    zusammen = " ".join(t["text"] for t in r["texte"])
    assert "Küche" in zusammen


def test_konfidenz_schwelle_verwirft():
    r = ocr.ocr_bild(_png([(60, 120, "1:50")]), min_conf=100)  # nichts erreicht conf 100
    assert r["texte"] == []
    assert r["ocr_verfuegbar"] is True


def test_graceful_off_ohne_ocr(monkeypatch):
    monkeypatch.setattr(ocr, "verfuegbar", lambda: False)
    assert ocr.ocr_bild(b"egal") == {"texte": [], "ocr_verfuegbar": False}
