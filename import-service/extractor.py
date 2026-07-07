"""DXF-Rohextraktion mit ezdxf → kanonisches Kandidaten-Geometrie-JSON (Koordinaten in mm).

Nur Rohextraktion: Einheit/Maßstab, bbox, Layer, Linien/Polylinien, DIMENSION-Maße, Texte.
KEINE Wand-Rekonstruktion/Topologie (das ist A-3b-2 im Editor).
"""

import ezdxf

# $INSUNITS-Code → (Einheitsname, Faktor Zeichnungseinheit → mm)
UNITS = {
    0: ("unbekannt", 1.0),
    1: ("inch", 25.4),
    2: ("foot", 304.8),
    4: ("mm", 1.0),
    5: ("cm", 10.0),
    6: ("m", 1000.0),
}


def extract_dxf(pfad: str) -> dict:
    doc = ezdxf.readfile(pfad)
    msp = doc.modelspace()

    insunits = int(doc.header.get("$INSUNITS", 0) or 0)
    einheit, faktor = UNITS.get(insunits, ("unbekannt", 1.0))

    xs: list[float] = []
    ys: list[float] = []

    def pt(x, y):
        mx, my = round(x * faktor, 1), round(y * faktor, 1)
        xs.append(mx)
        ys.append(my)
        return [mx, my]

    entities = []
    layer_counts: dict[str, int] = {}

    def zaehle(layer):
        layer_counts[layer] = layer_counts.get(layer, 0) + 1

    for e in msp:
        t = e.dxftype()
        layer = e.dxf.layer
        if t == "LINE":
            entities.append({"layer": layer, "typ": "line", "punkte": [pt(e.dxf.start.x, e.dxf.start.y), pt(e.dxf.end.x, e.dxf.end.y)]})
            zaehle(layer)
        elif t == "LWPOLYLINE":
            punkte = [pt(p[0], p[1]) for p in e.get_points("xy")]
            entities.append({"layer": layer, "typ": "lwpolyline", "punkte": punkte, "geschlossen": bool(e.closed)})
            zaehle(layer)
        elif t == "POLYLINE":
            punkte = [pt(v.dxf.location.x, v.dxf.location.y) for v in e.vertices]
            entities.append({"layer": layer, "typ": "polyline", "punkte": punkte, "geschlossen": bool(e.is_closed)})
            zaehle(layer)

    dimensions = []
    for d in msp.query("DIMENSION"):
        try:
            gemessen = round(float(d.get_measurement()) * faktor, 1)
        except Exception:
            gemessen = None
        dimensions.append({"text": str(d.dxf.get("text", "<>")), "gemessen_mm": gemessen})

    texte = []
    for e in msp.query("TEXT MTEXT"):
        inhalt = e.plain_text() if hasattr(e, "plain_text") else (e.text if e.dxftype() == "MTEXT" else e.dxf.text)
        ins = e.dxf.insert
        texte.append({"layer": e.dxf.layer, "text": str(inhalt), "position": {"x": round(ins.x * faktor, 1), "y": round(ins.y * faktor, 1)}})

    bbox = None
    if xs:
        bbox = {"min": {"x": min(xs), "y": min(ys)}, "max": {"x": max(xs), "y": max(ys)}}

    return {
        "einheit": einheit,
        "massstab_mm_pro_einheit": faktor,
        "bbox": bbox,
        "layers": [{"name": n, "entity_count": c} for n, c in sorted(layer_counts.items())],
        "entities": entities,
        "dimensions": dimensions,
        "texte": texte,
        "konfidenz": "hoch",
    }
