# W-17 · Export und Speichern — FUNKTION

> **Ablesung des vorhandenen Codes, nicht Vorgabe.** *Jede Zeilenangabe ist einzeln geöffnet worden;
> keine Zahl stammt aus einem anderen Blatt.*

## Der Weg vom Knopf bis in die Datenbank

```text
1  INSEL        store/hausplanerStore.ts:208  save()
                :213  speicherStatus 'speichert'
                :216  PUT auf speichernUrl, credentials same-origin, X-CSRF-TOKEN
                :224  Rumpf: base_revision, schema_version, scene

2  EINGANG      Requests/SpeichereHausplanerDokumentRequest.php
                :23   base_revision  required integer min:1
                :46   schema_version required integer in:SCHEMA_VERSION
                :47   scene          required array
                :61   Groesse/JSON
                :75   scene.projectId muss zum OBJEKT gehoeren
                :77   Huelle und Szene muessen DIESELBE Schema-Version tragen
                :80   Huelle und Szene muessen DIESELBE base_revision tragen

3  SCHREIBEN    Actions/SpeichereHausplanerDokument.php:24  DB::transaction
                :28   revision != base_revision  ->  ok:false + aktuelle revision
                :32   revision + 1
                :33   scene.revision auf die SERVER-Revision gesetzt
                :45   ok:true, revision, checksum

4  ZURUECK      store/hausplanerStore.ts:231  409  -> 'konflikt' + konfliktRevision
                :236  !ok -> 'fehler'
                :241  ok  -> revision uebernehmen
                :242  historie.markiereGespeichert()
                :245  catch -> 'fehler'
```

> ***Die Eingangsprüfung ist die interessanteste Schicht, und sie wird leicht übersehen.***
> *Drei ihrer Regeln vergleichen die **Hülle** mit der **Szene** selbst* (`:75`, `:77`, `:80`) —
> **`projectId`, `schemaVersion` und `revision` müssen an beiden Stellen dasselbe sagen.**
> *Ein Client, der die Hülle richtig und die Szene falsch füllt, kommt nicht durch.*

## Der 409-Pfad, und warum er kein Fehlerpfad ist

```text
Client base_revision 7   Server revision 9
  ->  Action gibt ok:false und die AKTUELLE revision zurueck
  ->  Controller antwortet 409
  ->  Store :231  speicherStatus 'konflikt', konfliktRevision 9
```

> **Der Anwender erfährt zwei Dinge:** *dass sein Stand überholt ist, und **welcher** jetzt gilt.*
> **Das zweite ist der Unterschied zwischen einer Meldung und einer brauchbaren Meldung** — *ohne
> die fremde Revision weiß er nicht, ob er ein paar Sekunden oder einen halben Tag verloren hat.*

## Der Rückweg ist APPEND-ONLY — die Historie läuft nie rückwärts

**`StelleSnapshotWieder.php:12-15`, im Kopf der Datei selbst begründet:**

```text
1) der AKTUELLE Stand wird VOR der Wiederherstellung selbst als Snapshot gesichert
2) die Snapshot-Szene wird zum neuen Stand mit revision + 1
```

> ***Wiederherstellen heißt hier: vorwärts auf einen alten Inhalt, nicht zurück auf eine alte
> Revision.*** *Damit kann ein Rückweg nie etwas vernichten* — **auch der Stand, von dem
> zurückgesprungen wird, ist danach noch da.** *Und die Revision wächst monoton, was den 409-Pfad
> überhaupt erst verlässlich macht: eine Revision, die zurückspringen könnte, wäre als Vergleich
> wertlos.*

## Die Historie überlebt das Speichern

**`store/hausplanerStore.ts:242`** — `historie.markiereGespeichert()`, *mit dem Kommentar
„Historie bleibt erhalten (Kante „Undo über Save-Grenze")".*

> **Nach dem Speichern kann der Anwender weiter rückgängig machen.** *Was sich ändert, ist nur die
> Antwort auf die Frage „ist etwas ungespeichert" —* `:244` *setzt den Status danach auf
> `ungespeichert`, **wenn die Historie das sagt**, sonst auf `gespeichert`.*

## Der Export ist eine ganz andere Sache

```text
app/HausplanerApp.tsx:664  exportPng()
                     :667  stage.toDataURL({ pixelRatio: 2 })
                     :670  a.download = 'grundriss.png'
```

> **Kein Server, keine Revision, kein Rückweg.** *Der Export verlässt das System und kommt nicht
> wieder herein* — *siehe `7-GRENZEN`.*
