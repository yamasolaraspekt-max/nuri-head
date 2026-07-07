"""Vektor-PDF-Extraktion mit PyMuPDF (fitz) → dieselbe Kandidaten-Geometrie-Struktur wie extractor.py.

PDF-Grundrisse liefern Vektorpfade in PDF-Punkten (1/72 Zoll). Der reale Gebäude-Maßstab ist
NICHT bekannt (anders als DXF mit $INSUNITS) → massstab_mm_pro_einheit bleibt null; die Kalibrierung
("bekannte Strecke anklicken + Maß eingeben") folgt im Editor (A-3c). Bis dahin ist der Underlay
NICHT maßhaltig und darf nicht ins Nachzeichnen (Editor-Link bleibt gesperrt).

Koordinaten y-up (CAD-Konvention, wie extractor.py), damit Underlay-Orientierung konsistent bleibt.
Klassifiziert zusätzlich pdf_typ = vektor|raster (Vektorpfade vorhanden vs. reines Rasterbild).
KEINE Wand-Rekonstruktion/Topologie — das ist der Editor.
"""

import fitz

PUNKT_PRO_ZOLL = 72.0


def _hex(color) -> str:
    if not color:
        return "000000"
    r, g, b = (max(0, min(255, round(c * 255))) for c in color[:3])
    return f"{r:02x}{g:02x}{b:02x}"


def _groesste_seite(doc):
    """Grundriss ist i.d.R. die flächengrößte Seite eines PDFs."""
    best = doc[0]
    for seite in doc:
        if seite.rect.width * seite.rect.height > best.rect.width * best.rect.height:
            best = seite
    return best


def extract_pdf(pfad: str) -> dict:
    doc = fitz.open(pfad)
    try:
        seite = _groesste_seite(doc)
        hoehe = seite.rect.height  # für y-up-Flip
        seiten = doc.page_count

        xs: list[float] = []
        ys: list[float] = []

        def pt(p) -> list[float]:
            x, y = round(p.x, 1), round(hoehe - p.y, 1)  # y-up wie CAD
            xs.append(x)
            ys.append(y)
            return [x, y]

        entities: list[dict] = []
        layer_counts: dict[str, int] = {}

        def zaehle(layer: str) -> None:
            layer_counts[layer] = layer_counts.get(layer, 0) + 1

        for d in seite.get_drawings():
            layer = "farbe_" + _hex(d.get("color") or d.get("fill"))
            for it in d["items"]:
                art = it[0]
                if art == "l":  # Linie
                    entities.append({"layer": layer, "typ": "line", "punkte": [pt(it[1]), pt(it[2])]})
                    zaehle(layer)
                elif art == "c":  # Bezier → grobe Sehne (Endpunkte); Mensch zieht nach
                    entities.append({"layer": layer, "typ": "line", "punkte": [pt(it[1]), pt(it[4])]})
                    zaehle(layer)
                elif art == "re":  # Rechteck
                    r = it[1]
                    punkte = [pt(fitz.Point(r.x0, r.y0)), pt(fitz.Point(r.x1, r.y0)), pt(fitz.Point(r.x1, r.y1)), pt(fitz.Point(r.x0, r.y1))]
                    entities.append({"layer": layer, "typ": "lwpolyline", "punkte": punkte, "geschlossen": True})
                    zaehle(layer)
                elif art == "qu":  # Quad
                    q = it[1]
                    punkte = [pt(q.ul), pt(q.ur), pt(q.lr), pt(q.ll)]
                    entities.append({"layer": layer, "typ": "lwpolyline", "punkte": punkte, "geschlossen": True})
                    zaehle(layer)

        texte = []
        for w in seite.get_text("words"):
            x0, y0, x1, y1, wort = w[0], w[1], w[2], w[3], w[4]
            texte.append({"layer": "text", "text": wort, "position": {"x": round((x0 + x1) / 2, 1), "y": round(hoehe - (y0 + y1) / 2, 1)}})

        pdf_typ = "vektor" if entities else "raster"

        bbox = None
        if xs:
            bbox = {"min": {"x": min(xs), "y": min(ys)}, "max": {"x": max(xs), "y": max(ys)}}

        return {
            "einheit": "punkt",
            "massstab_mm_pro_einheit": None,  # unbekannt — Kalibrierung im Editor (A-3c)
            "pdf_typ": pdf_typ,
            "seiten": seiten,
            "bbox": bbox,
            "layers": [{"name": n, "entity_count": c} for n, c in sorted(layer_counts.items())],
            "entities": entities,
            "dimensions": [],
            "texte": texte,
            "konfidenz": "mittel" if pdf_typ == "vektor" else "niedrig",
        }
    finally:
        doc.close()
