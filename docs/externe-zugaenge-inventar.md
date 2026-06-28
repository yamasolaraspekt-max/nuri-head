# Inventar: Externe Konten & Zugänge

**Stand:** 2026-06-28 · Reine Doku — **keine Secret-Werte**, nur Dienst, Ort im Code, Eigentümer-Hinweis (aus Code/Config gelesen) und konkreter nächster Schritt. Zweck: klären, **wem der Zugang gehört** und ob **du selbst Zugriff** hast.

## Wichtigste Eigentümer-Hinweise (nur was im Code/Config steht)
- **Ramin Sadid** (Ex-Programmierer): erster Git-Commit (`Ramin Sadid`), `origin`-Repo = `raminsadid2021`. Ihm gehört das Original-Repo (und das Tomorrow.io-Konto).
- **Solar Aspekt** (Firma): Mail-Absender „SOLAR ASPEKT", `leads@solar-aspekt.de`, Bitrix-Konto `solaraspekt`, Mapbox-Konto `solar-aspekt`.
- **„Nuri"** = dein Nachname (Yama **Nuri**): Domains `nuri-head.de`, `nuri-software.de` (`hallo@nuri-software.de`).
- **Mail läuft über Goneo** (`smtp.goneo.de`, Konto `noreply@nuri-head.de`) — **nicht** kasserver (das war nur ein ungenutzter Config-Standardwert).

---

## 🔴 Existenzbedrohend, wenn du KEINEN Zugang hast
| Dienst | Wo referenziert | Eigentümer-Hinweis | Schlimmster Fall ohne Zugang | Dein Zugang? | Status / nächster Schritt |
|---|---|---|---|---|---|
| **Domain `nuri-head.de`** (DNS/Registrar) | `ApiLinkController` redirectUri, `.env MAIL_FROM` | „Nuri" = dein Name; `noreply@nuri-head.de` | **Website + Mail komplett tot** | ❓ | **DENIC-WebWhois prüfen** (webwhois.denic.de) — läuft sie auf Solar Aspekt oder Privatperson? Registrar + Inhaber feststellen. |
| **Webhosting / Server** (nuri-head.de) | nicht im Code; Indiz: Mail über **Goneo** | vermutlich Goneo | **Kein Deploy, kein Zugriff auf Live-App** | ❓ | Klären, wo nuri-head.de gehostet ist (zuerst Goneo prüfen) und **Hosting-/Server-Login besorgen**. |
| **Mail (Goneo)** `smtp.goneo.de` | `config/mail.php`, `.env MAIL_HOST/USERNAME` | Konto `noreply@nuri-head.de` | **Keine System-Mails** (Benachrichtigungen, Passwort-Resets) | ❓ | **Zugangsdaten zum Goneo-Konto** (noreply@nuri-head.de) suchen/klären. |
| **GitHub `origin`** | `git remote origin` | **Ramin Sadid** (`raminsadid2021`) | gehört Ramin — aber privates Backup existiert | ❌ (nur Lesen) | **Erledigt** — privates Backup `nurihead` existiert. Nur klären, ob ein **gemeinsames Repo mit Ramin** gewünscht ist. |

## 🟠 Wichtig (Geschäfts-Integrationen brechen)
| Dienst | Wo referenziert | Eigentümer-Hinweis | Schlimmster Fall | Dein Zugang? | Status / nächster Schritt |
|---|---|---|---|---|---|
| **IDS / GC Online** (Artikel-Shop) | `services.ids`, `.env IDS_*` | **Kundennr. 017896**, User 160160017896 | Artikel-/Shop-Sync bricht | ❓ | GC-Online-**Händlerkonto (Kundennr. 017896)** zuordnen — auf wessen Namen? Login besorgen. |
| **Bitrix24** (Chat) | `MessageController`, Bitrix | Konto **`solaraspekt`** | Chat-Integration tot | ❓ | **Bitrix24-Admin-Zugang** für `solaraspekt.bitrix24.de` klären. |
| **myUplink / NIBE** (Wärmepumpen) | `ApiLinkController`, `.env MYUPLINK_*` | OAuth-App, Redirect `nuri-head.de` | Wärmepumpen-Daten weg | ❓ | **NIBE/myUplink-Entwicklerkonto** identifizieren, Zugang besorgen. |
| **Google Maps** | viele Views, `.env GOOGLE_MAPS_KEY` | Google-Cloud-Projekt | Karten aus (App läuft sonst) | ❓ | **Google-Cloud-Konto** mit dem Maps-Key identifizieren; Key per Referrer einschränken (offener Audit-Punkt). |
| **Mapbox** | `prof.blade`, `.env MAPBOX_TOKEN` | Konto **`solar-aspekt`** | eine Adress-Karte aus | ❓ | **Mapbox-Konto `solar-aspekt`** — Login klären; Token per URL einschränken. |
| **AWS** (S3/SES) | `services.ses`, filesystems | Bucket **leer**, Mail über Goneo | wohl **ungenutzt** → kaum Schaden | ❓ | Prüfen, ob AWS überhaupt genutzt wird — sonst aus Config entfernen/ignorieren. |

## 🟢 Harmlos / geklärt
| Dienst | Wo | Eigentümer | Status / nächster Schritt |
|---|---|---|---|
| **Tomorrow.io** (Wetter) | `EmployeeDashboardController` | **Ex-Programmierer** (kostenlos) | ✅ **Geklärt/unkritisch** — später eigenes kostenloses Konto anlegen (TODO steht in `.env`). |
| **OpenWeatherMap, RapidAPI, NewsAPI** | `services.*` | ❓ (unkritisch) | ✅ **Unkritisch** — bei Bedarf eigenes kostenloses Konto. |
| Open-Meteo, DWD, PVGIS, Overpass, Geo-IP, ui-avatars | div. | **kein Konto** (frei) | ✅ Kein Handlungsbedarf. |

---

## 👉 Priorität zum Nachhaken
1. **Domain `nuri-head.de`** (WebWhois) — wichtigster Punkt: ohne sie sind Website + Mail weg.
2. **Goneo-Konto** (Hosting + Mail).
3. **GitHub origin (Ramin)** — Backup erledigt; nur gemeinsames Repo klären.
4. **IDS 017896, Bitrix `solaraspekt`, Google/Mapbox, NIBE** — Inhaber + Login klären.
