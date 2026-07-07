"""FastAPI-Microservice für den Plan-Import (A-3b: DXF/ezdxf + Vektor-PDF/PyMuPDF).

Start (lokal):  uvicorn main:app --port 8001
Laravel ruft /extract/dxf bzw. /extract/pdf per multipart; ist IMPORT_SERVICE_URL leer,
wird der Service nie aufgerufen.
"""

import os
import tempfile

import ezdxf
import fitz
from fastapi import FastAPI, File, HTTPException, UploadFile
from fastapi.responses import Response

from extractor import extract_dxf
from ocr import ocr_bild
from ocr import verfuegbar as ocr_verfuegbar
from pdf_extractor import extract_pdf
from pdf_raster import PdfGeschuetzt, rasterize_pdf

app = FastAPI(title="wberechnung import-service")


@app.get("/health")
def health():
    return {
        "status": "ok",
        "ezdxf_version": ezdxf.__version__,
        "pymupdf_version": fitz.pymupdf_version,
        "ocr_verfuegbar": ocr_verfuegbar(),
    }


@app.post("/ocr")
async def ocr_endpoint(datei: UploadFile = File(...)):
    # Läuft auf dem vorhandenen Underlay-Bild; graceful-off liefert texte:[] statt Fehler.
    return ocr_bild(await datei.read())


async def _extrahiere(datei: UploadFile, fallback_name: str, funktion, fehlerlabel: str):
    suffix = os.path.splitext(datei.filename or fallback_name)[1] or os.path.splitext(fallback_name)[1]
    tmp = tempfile.NamedTemporaryFile(delete=False, suffix=suffix)
    try:
        tmp.write(await datei.read())
        tmp.close()
        return funktion(tmp.name)
    except Exception as e:  # noqa: BLE001 — Klartextfehler an Laravel
        raise HTTPException(status_code=422, detail=f"{fehlerlabel} fehlgeschlagen: {e}")
    finally:
        if os.path.exists(tmp.name):
            os.unlink(tmp.name)


@app.post("/extract/dxf")
async def extract_dxf_endpoint(datei: UploadFile = File(...)):
    return await _extrahiere(datei, "plan.dxf", extract_dxf, "DXF-Extraktion")


@app.post("/extract/pdf")
async def extract_pdf_endpoint(datei: UploadFile = File(...)):
    return await _extrahiere(datei, "plan.pdf", extract_pdf, "PDF-Extraktion")


@app.post("/rasterize/pdf")
async def rasterize_pdf_endpoint(datei: UploadFile = File(...)):
    suffix = os.path.splitext(datei.filename or "plan.pdf")[1] or ".pdf"
    tmp = tempfile.NamedTemporaryFile(delete=False, suffix=suffix)
    try:
        tmp.write(await datei.read())
        tmp.close()
        r = rasterize_pdf(tmp.name)
        return Response(
            content=r["png"],
            media_type="image/png",
            headers={
                "X-Breite": str(r["breite"]),
                "X-Hoehe": str(r["hoehe"]),
                "X-Quelle-Seite": str(r["quelle_seite"]),
                "X-Seiten-Gesamt": str(r["seiten_gesamt"]),
                "X-Effektive-DPI": str(r["effektive_dpi"]),
            },
        )
    except PdfGeschuetzt as e:
        raise HTTPException(status_code=422, detail=str(e))
    except Exception as e:  # noqa: BLE001 — Klartextfehler an Laravel
        raise HTTPException(status_code=422, detail=f"PDF-Rasterung fehlgeschlagen: {e}")
    finally:
        if os.path.exists(tmp.name):
            os.unlink(tmp.name)
