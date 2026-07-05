<?php

namespace Database\Factories;

use App\Models\Anforderungsprofil;
use App\Models\LeadAlternativeAdd;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<Anforderungsprofil>
 */
class AnforderungsprofilFactory extends Factory
{
    protected $model = Anforderungsprofil::class;

    public function definition(): array
    {
        return [
            'verankerbar_type' => LeadAlternativeAdd::class,
            'verankerbar_id' => fn () => static::objektAnker()->getKey(),
            'version' => 1,
            'status' => Anforderungsprofil::STATUS_ENTWURF,
            'bezeichnung' => 'Bestand',
            'created_by' => null,
        ];
    }

    /** An eine konkrete (geteilte) Verankerung binden. */
    public function fuer(Model $verankerbar): static
    {
        return $this->state(fn () => [
            'verankerbar_type' => $verankerbar->getMorphClass(),
            'verankerbar_id' => $verankerbar->getKey(),
        ]);
    }

    public function aktiv(): static
    {
        return $this->state(fn () => ['status' => Anforderungsprofil::STATUS_AKTIV]);
    }

    /** Minimaler gültiger Objekt-Anker (new_leads -> lead_alternative_adds). */
    public static function objektAnker(): LeadAlternativeAdd
    {
        $leadId = DB::table('new_leads')->insertGetId([
            'customer_type' => 'private', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $id = DB::table('lead_alternative_adds')->insertGetId([
            'lead_id' => $leadId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return LeadAlternativeAdd::findOrFail($id);
    }
}
