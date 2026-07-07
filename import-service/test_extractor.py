"""Tests der DXF-Rohextraktion (ezdxf). Lauf: cd import-service && .venv/bin/pytest -q"""

import os
import tempfile

import ezdxf

from extractor import extract_dxf


def _rechteck_dxf(units, mass):
    doc = ezdxf.new("R2010", units=units)
    doc.layers.add("WALL")
    msp = doc.modelspace()
    a = mass
    pts = [(0, 0), (5 * a, 0), (5 * a, 6 * a), (0, 6 * a)]
    for i in range(4):
        msp.add_line(pts[i], pts[(i + 1) % 4], dxfattribs={"layer": "WALL"})
    if units == ezdxf.units.MM:
        dim = msp.add_linear_dim(base=(0, -500), p1=(0, 0), p2=(5000, 0), dxfattribs={"layer": "DIM"})
        dim.render()
    f = tempfile.NamedTemporaryFile(delete=False, suffix=".dxf")
    f.close()
    doc.saveas(f.name)
    return f.name


def test_mm_rechteck_mit_dimension():
    pfad = _rechteck_dxf(ezdxf.units.MM, 1000)  # 5×6 m als 5000×6000 mm-Einheiten
    try:
        r = extract_dxf(pfad)
    finally:
        os.unlink(pfad)
    assert r["einheit"] == "mm"
    assert r["massstab_mm_pro_einheit"] == 1.0
    assert r["bbox"]["max"]["x"] == 5000 and r["bbox"]["max"]["y"] == 6000
    wall = next(layer for layer in r["layers"] if layer["name"] == "WALL")
    assert wall["entity_count"] == 4
    assert any(abs((d["gemessen_mm"] or 0) - 5000) < 1 for d in r["dimensions"])
    assert r["konfidenz"] == "hoch"


def test_meter_zeichnung_faktor_1000():
    pfad = _rechteck_dxf(ezdxf.units.M, 1)  # Einheit Meter (5 → 5 m)
    try:
        r = extract_dxf(pfad)
    finally:
        os.unlink(pfad)
    assert r["einheit"] == "m"
    assert r["massstab_mm_pro_einheit"] == 1000.0
    assert r["bbox"]["max"]["x"] == 5000  # 5 m → 5000 mm
    assert r["bbox"]["max"]["y"] == 6000
