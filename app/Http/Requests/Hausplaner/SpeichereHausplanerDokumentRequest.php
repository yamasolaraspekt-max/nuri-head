<?php

namespace App\Http\Requests\Hausplaner;

use App\Domain\Hausplaner\Models\HausplanerDocument;
use App\Domain\Hausplaner\Validation\SceneDocumentValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SpeichereHausplanerDokumentRequest extends FormRequest
{
    private const MAX_SCENE_BYTES = 2_000_000;

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'base_revision' => ['required', 'integer', 'min:1'],
            // Z-06-N1: die Insel hebt auf v3 (geometrieHerkunft/freigabe an Decke und Dach).
            //
            // **`in:3`, nicht `in:2,3` — und der erste Versuch war der zweite.** Die Regel ist
            // nicht die einzige Schranke: `SceneDocumentValidator` prüft die Szene gegen das AUS
            // ZOD ERZEUGTE Schema (`scene-document-v2.schema.json`), und das verlangt seit dem
            // N1-Bau v3 samt Pflichtfeldern. *Eine Regel, die 2 durchlässt, während der Validator
            // dahinter 2 ablehnt, verschiebt den Fehler nur eine Ebene tiefer und macht aus einer
            // klaren Versionsmeldung eine Schema-Fehlerliste.* Zwei Schranken, eine Wahrheit.
            //
            // **Der Ladepfad bleibt unberührt:** der Validator hängt AUSSCHLIESSLICH am Speichern
            // (gemessen: eine Fundstelle, `withValidator`). Abgelegte v1-/v2-Dokumente werden
            // weiterhin unverändert ausgeliefert und von der Insel beim Laden migriert.
            //
            // ⚠ **OFFEN, und es gehört nicht in diese Datei:** `public/hausplaner/` trägt noch
            // den v2-Bau. Bis `npm run build:hausplaner` gelaufen ist, schickt die ausgelieferte
            // Seite eine 2 und bekommt 422. *Das war schon vor dieser Zeile so — die Regel
            // verursacht es nicht, sie benennt es nur ehrlich.*
            //
            // Gefunden hat die Lücke der Evaluator: die Insel-Suite (1667/0) kennt den Server
            // nicht, und die Sichtprobe des Blattes zeigte auf `studio.blade` — eine Fläche
            // OHNE Persistenz. *Eine P1-Zusage, die auf einer Fläche ohne Speichern prüft, ob
            // etwas das Speichern überlebt, kann weder grün noch rot werden.*
            'schema_version' => ['required', 'integer', 'in:3'],
            'scene' => ['required', 'array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->count() > 0) {
                return;
            }

            $scene = $this->input('scene');
            $json = json_encode($scene, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json === false || strlen($json) > self::MAX_SCENE_BYTES) {
                $validator->errors()->add('scene', 'Die Szene überschreitet die zulässige Größe oder ist kein gültiges JSON.');

                return;
            }

            foreach (app(SceneDocumentValidator::class)->fehler($scene) as $fehler) {
                $validator->errors()->add('scene', $fehler);
            }
            if ($validator->errors()->count() > 0) {
                return;
            }

            $objektId = (int) $this->route('objekt')->getKey();
            if ((int) $scene['projectId'] !== $objektId) {
                $validator->errors()->add('scene.projectId', 'Die Szene gehört nicht zu diesem Objekt.');
            }
            if ((int) $scene['schemaVersion'] !== (int) $this->input('schema_version')) {
                $validator->errors()->add('schema_version', 'Hülle und Szene verwenden unterschiedliche Schema-Versionen.');
            }
            if ((int) $scene['revision'] !== (int) $this->input('base_revision')) {
                $validator->errors()->add('base_revision', 'Hülle und Szene verwenden unterschiedliche Revisionen.');
            }

            $dokument = HausplanerDocument::query()->where('alternative_id', $objektId)->first();
            if ($dokument !== null && (string) $scene['id'] !== (string) ($dokument->scene_json['id'] ?? '')) {
                $validator->errors()->add('scene.id', 'Die Dokument-ID stimmt nicht mit dem gespeicherten Plan überein.');
            }
        });
    }

    /** @return array<string, mixed> */
    public function scene(): array
    {
        return $this->validated('scene');
    }
}
