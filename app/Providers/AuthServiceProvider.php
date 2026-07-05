<?php

namespace App\Providers;
use App\Models\PersonalTask;
use App\Policies\PersonalTaskPolicy;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\ChatGroup;
use App\Policies\ChatGroupPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
        PersonalTask::class => PersonalTaskPolicy::class,
        ChatGroup::class => ChatGroupPolicy::class,
        \App\Models\AiChat::class => \App\Policies\AiChatPolicy::class,
        \App\Models\GeneralTask::class => \App\Policies\GeneralTaskPolicy::class, // FIX P0-10
        \App\Models\DealMeasurement::class => \App\Policies\DealMeasurementPolicy::class, // S-1a Ownership
    ];

    /**
     * Register any authentication / authorization services.
     */
   public function boot()
    {
        $this->registerPolicies();

        Gate::define('manage-chat-groups', function ($user) {
            // allow only users with role = Admin, or any logic
            return $user->role === 'Admin';
        });

        // S-1b-1 (W-0): Deal-Anker für Offer-Ebene-Writes ohne Aufmaß. deals hat kein created_by ->
        // Owner = deals.employee_id (FK, non-null) + Super-Admin; Portal-Hart-Deny ohne Employee-Kontext.
        Gate::define('write-deal-measurement-offer', function (\App\Models\User $user, \App\Models\Deal $deal) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            $emp = $user->employeeId();
            if ($emp === null) {
                return false;
            }

            return (string) $emp === (string) $deal->employee_id;
        });

        // S-1b-2: Bild löschen. Image trägt keinen Measurement-Link -> Kunden-Anker (b+-Kette):
        // Uploader (image.created_by) ∨ Deal-Zuständiger des Kunden ∨ write-Beteiligter auf einem
        // Aufmaß des Kunden (Ersteller/Techniker) ∨ Admin. Portal-Hart-Deny; Unbeteiligte hart (+Log).
        Gate::define('delete-measurement-image', function (\App\Models\User $user, \App\Models\Image $image) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            $emp = $user->employeeId();
            if ($emp === null) {
                return false;
            }
            if ((string) $image->created_by === (string) $emp) {
                return true; // Uploader
            }
            if (\App\Models\Deal::where('customer_id', $image->customer_id)->where('employee_id', $emp)->exists()) {
                return true; // Deal-Zuständiger des Kunden
            }
            $dealIds = \App\Models\Deal::where('customer_id', $image->customer_id)->pluck('id');
            if (\App\Models\DealMeasurement::whereIn('deal_id', $dealIds)
                ->where(fn ($q) => $q->where('created_by', $emp)->orWhere('responsible_employee_id', $emp))->exists()) {
                return true; // write-Beteiligter auf einem Aufmaß des Kunden
            }

            \Illuminate\Support\Facades\Log::warning('deal_measurement_ability_soft_deny', [
                'ability' => 'image_delete_denied', 'image_id' => $image->id, 'employee_id' => $emp,
            ]);
            \Illuminate\Support\Facades\Cache::put('image_delete_denied_count', (int) \Illuminate\Support\Facades\Cache::get('image_delete_denied_count', 0) + 1);

            return false;
        });
    }
}
