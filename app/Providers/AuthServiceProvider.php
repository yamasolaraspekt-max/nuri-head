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
    }
}
