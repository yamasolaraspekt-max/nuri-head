# Wie und wo läuft `ticket`? — Status (in einfacher Sprache)

**Stand:** 2026-06-28 · **Reine Lese-/Analyse-Aufgabe** — nichts geändert, nichts gestartet, kein fremder Server angefasst. Alles unten stammt aus **Code, Konfiguration und den gerade laufenden Prozessen auf deinem Mac**.

---

## 1. Läuft das lokal oder „live"?

**Antwort: Es läuft gerade nur LOKAL auf deinem Rechner.** Ein echtes Live-/Produktiv-System gibt es laut Code und Konfiguration **nicht**.

Was gerade auf deinem Mac läuft (über **Laravel Herd** — das ist eine lokale Entwicklungs-Umgebung):
| Prozess | Was es ist | Lokal oder Live? |
|---|---|---|
| `Herd.app` + `dnsmasq` + Herd-Helper | Herd selbst — deine lokale Mac-Entwicklungsumgebung (macht u. a. `.test`-Adressen möglich) | **nur lokal** |
| `php84 -S 127.0.0.1:8002 …/ticket/…` | ticket wird ausgeliefert — auf `127.0.0.1` = **dein eigener Rechner** | **nur lokal** |
| `php84 -S 127.0.0.1:8000 …/Playground/…` | das **andere** Projekt (playground) — läuft parallel, auch nur lokal | **nur lokal** |
| `nginx` + `php-fpm: pool herd` | der lokale Webserver von Herd | **nur lokal** |
| `node …/ticket/node_modules/.bin/vite` + `npm run dev` | **Vite** — baut beim Entwickeln live deine CSS/JS-Dateien (Asset-Watcher) | **nur lokal** (reines Entwickler-Werkzeug) |

Merksatz: **`127.0.0.1` bzw. `localhost` heißt immer „mein eigener Rechner".** Alles, was du oben siehst, ist deine **persönliche Arbeitsumgebung** — kein Server, den Kunden oder Kollegen erreichen.

---

## 2. Wie ist das Deployment (die Veröffentlichung) konfiguriert?

**Antwort: Gar nicht.** Es gibt im Projekt **keinerlei** Hinweise auf eine Veröffentlichung auf einem Server:
- ❌ Kein `.github/workflows`, keine CI/CD-Pipeline (nichts, was automatisch auf einen Server hochlädt).
- ❌ Keine Deploy-Skripte (`deploy.php`, `Envoy.blade.php`, `Procfile`, `fly.toml`, Forge/Ploi-Spuren …).
- ❌ Kein `docker-compose.yml`. (Der Ordner `docker/` ist nur ungenutztes Standard-Gerüst von „Laravel Sail" — wird hier nicht verwendet.)
- ❌ Keine produktive Server-/nginx-/Apache-Konfiguration im Projekt.
- ⚙️ `APP_ENV=local`, `APP_DEBUG=true`, `APP_URL=http://ticket.test` → die Konfiguration sagt **selbst**: „Entwicklungs-Umgebung", nicht „Produktion".

**Hinweise auf MEHRERE Server / Lastverteilung?** **Keine.** Kein Load Balancer, keine mehreren App-Hosts, keine getrennten DB-/Cache-/Datei-Server. Alles deutet auf **eine einzige Maschine** (deinen Rechner) hin.

**Hoster/Domain — Vorsicht vor Fehldeutung:**
- Der README-Treffer **„Webdock"** ist **kein** Hoster-Hinweis von ticket — das ist die **Standard-Sponsorenliste**, die in jeder Laravel-`README.md` steht. Bitte ignorieren.
- `MAIL_HOST=smtp.goneo.de` (**Goneo**) ist nur der **Postausgang** zum **E-Mail-Versenden** — **nicht** der Ort, wo die App läuft.
- Deine Domain **nuri-head.de** taucht in der App-Konfiguration als App-Standort **nicht** auf (sie wird nur bei externen Schnittstellen als Rücksprung-Adresse genutzt). Es gibt also **keinen** konfigurierten Live-Server.

---

## 3. Könnte ticket überhaupt auf mehrere Server verteilt werden?

Das hängt davon ab, **wo** ticket Sessions, Cache und Dateien ablegt. Aktuell:

| Was | Eingestellt auf | Bedeutung für „mehrere Server" |
|---|---|---|
| **Anmelde-Sessions** | `SESSION_DRIVER=file` (Dateien auf der lokalen Festplatte) | 🔴 **Blocker** — bei mehreren Servern würden Nutzer ständig ausgeloggt (jeder Server kennt nur seine eigenen Sessions) |
| **Hochgeladene Dateien** | `FILESYSTEM_DISK=local` (lokale Festplatte) | 🔴 **Blocker** — Datei auf Server A wäre auf Server B nicht sichtbar |
| **Cache** | `CACHE_DRIVER=file` (lokale Festplatte) | 🟠 nicht geteilt — sollte zentral werden |
| **Datenbank** | MySQL auf `localhost`, DB `ticket` | 🟠 müsste auf einen **gemeinsamen** DB-Server umziehen |
| **Hintergrund-Jobs (Queue)** | `QUEUE_CONNECTION=database` (in der DB) | 🟢 **gut** — das wäre über mehrere Server hinweg teilbar |
| **Redis** | `REDIS_HOST=127.0.0.1` ist eingetragen, **wird aber nicht genutzt** (Sessions/Cache stehen auf „file") | — vorhanden, aber inaktiv |
| **Echtzeit (Reverb/WebSockets)** | `BROADCAST=reverb` konfiguriert | ⚙️ eigener Server-Prozess nötig; läuft aktuell **nicht** |

**Hintergrund-Jobs:** Es gibt **9 Job-Klassen** und **27 Klassen mit `ShouldQueue`** (Aufgaben, die im Hintergrund laufen sollen). Sie landen in der Datenbank — **aber:** aktuell läuft **kein** Arbeiter-Prozess (`php artisan queue:work`). Solche Jobs würden sich also derzeit nur ansammeln, bis ein Worker gestartet wird.

**Fazit zur Skalierung:** ticket ist heute als **„Eine-Maschine-App"** eingerichtet. Um es sauber auf mehrere Server zu verteilen, müsste man **vorher umbauen**: Sessions + Cache auf Redis oder Datenbank, hochgeladene Dateien auf einen **zentralen** Speicher (z. B. S3 oder geteiltes Netzlaufwerk), eine **gemeinsame** Datenbank, und Queue-Worker/Reverb als eigene Dienste. Technisch alles machbar — aber **ein bewusster Umbau, kein Schalter**.

---

## 4. Ehrliche Grenze — was ich weiß vs. was nur am Server prüfbar ist

**Das konnte ich sicher aus Code/Konfiguration ablesen:**
- ticket läuft hier **lokal** über Herd; alle Prozesse auf `127.0.0.1`.
- Es gibt **keine** Deployment-/CI-/Server-Konfiguration im Projekt.
- Die App ist als **Einzel-Maschine** konfiguriert (file-Sessions, file-Cache, lokale Dateien).

**Das ist NICHT aus dem Projekt heraus feststellbar (nur mit echtem Server-Zugang):**
- Ob **irgendwo trotzdem** ein Live-Server läuft, von dem dieses Projekt nichts „weiß" (z. B. eine ältere Kopie auf einem Webspace). Im Code gibt es **null** Hinweise darauf — aber ausschließen kann man es nur, indem ein Mensch mit Zugang beim Hoster nachschaut.
- **Wichtig:** Es ist **kein** Server-Zugang konfiguriert (keine SSH-Ziele, keine Deploy-Adresse). Ich **rate nicht** und greife **auf keine fremden Server** zu.

**Was du (oder jemand mit Zugang) selbst nachsehen müsste, um ganz sicher zu sein:**
1. Beim Hoster/Domain-Anbieter von **nuri-head.de** einloggen: Gibt es dort überhaupt einen Webspace/Server, auf dem etwas läuft?
2. Im **Goneo-Konto** prüfen, ob dort (außer E-Mail) auch Webhosting gebucht ist und ob ticket dort liegt.
3. Falls ein Server existiert: per **SSH/Hosting-Panel** schauen, ob dort eine ticket-Installation und laufende Prozesse (php-fpm, queue:work) vorhanden sind.

---

## 🧾 Fazit in 3 Sätzen (für Nicht-Techniker)
1. **ticket läuft aktuell nur auf einem einzigen Rechner — deinem — als lokale Entwicklungs-Version** (über Laravel Herd, Adresse `http://ticket.test`); ein echtes Live-/Produktiv-System gibt es laut Code und Konfiguration nicht.
2. Es ist als **„Eine-Maschine-App"** eingerichtet (Anmeldungen, Zwischenspeicher und hochgeladene Dateien liegen lokal auf der Festplatte), deshalb kann es **so wie es ist nicht einfach auf mehrere Server verteilt** werden.
3. Für mehrere Server müsste man es **erst umbauen** (zentrale Speicher für Sessions/Dateien/Cache, gemeinsame Datenbank) — das ist machbar, aber ein bewusster technischer Umbau und kein Knopfdruck.
