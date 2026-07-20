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
            'schema_version' => ['required', 'integer', 'in:2'],
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
