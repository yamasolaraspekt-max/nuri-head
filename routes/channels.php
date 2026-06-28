<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OfferFolder;
use App\Models\Employee;
use App\Models\LeadAlternativeAdd;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

Broadcast::routes([
    'middleware' => ['web', 'auth'],
]);

/*
|--------------------------------------------------------------------------
| Helper: resolve current authenticated user's employee profile
|--------------------------------------------------------------------------
| In this project users.name stores employees.id.
*/

if (!function_exists('broadcast_employee_payload')) {
    function broadcast_employee_payload($user): array
    {
        $employeeId = is_numeric($user?->name ?? null) ? (int) $user->name : null;
        $employee = $employeeId ? Employee::query()->find($employeeId) : null;

        $fullName = $employee
            ? trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''))
            : '';

        if ($fullName === '') {
            $fullName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
        }

        if ($fullName === '') {
            $fullName = $user->email ?? ('User #' . $user->id);
        }

        $employeeImage = $employee?->image ? ltrim((string) $employee->image, '/') : null;
        $avatar = $employeeImage
            ? asset('images/employee/' . $employeeImage)
            : asset('images/gender/male.png');

        return [
            // Echo presence identity
            'id' => (int) $user->id,
            'user_id' => (int) $user->id,

            // Your real CRM identity
            'employee_id' => $employee?->id,

            // Names used by different frontend renderers
            'name' => $fullName,
            'text' => $fullName,
            'display_name' => $fullName,
            'employee_name' => $fullName,
            'firstname' => $employee?->name,
            'lastname' => $employee?->lastname,

            // Images used by different frontend renderers
            'avatar' => $avatar,
            'image' => $employeeImage,
            'employee_image' => $employeeImage,
            'employee_avatar' => $avatar,

            'email' => $employee?->email ?? $user->email,
        ];
    }
}

// 1:1 chat channel between two users
Broadcast::channel('chat.{a}.{b}', function ($user, $a, $b) {
    return (int) $user->id === (int) $a || (int) $user->id === (int) $b;
});

// User-specific channel for direct notifications
Broadcast::channel('chat.user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Group chat channel – only subscribers can listen
Broadcast::channel('chat.group.{id}', function ($user, $id) {
    return DB::table('chat_group_user')
        ->where('chat_group_id', $id)
        ->where('user_id', $user->id)
        ->exists();
});

// Presence channel to track online users
Broadcast::channel('online', function ($user) {
    return broadcast_employee_payload($user);
});

Broadcast::channel('notifications.user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('ids', function () {
    return true;
});

Broadcast::channel('planner.plan.{planId}', function ($user, $planId) {
    return DB::table('planner_plans')
        ->where('id', (int) $planId)
        ->whereNull('deleted_at')
        ->exists();
});

Broadcast::channel('planner.employee.{employeeId}', function ($user, $employeeId) {
    $authEmployeeId = is_numeric($user?->name ?? null) ? (int) $user->name : null;

    if ($authEmployeeId && $authEmployeeId === (int) $employeeId) {
        return true;
    }

    if (isset($user->employee_id) && (int) $user->employee_id === (int) $employeeId) {
        return true;
    }

    return method_exists($user, 'can') ? $user->can('planner-admin') : false;
});

Broadcast::channel('planner.account.{accountId}', function ($user, $accountId) {
    return isset($user->account_id) && (int) $user->account_id === (int) $accountId;
});

/*
|--------------------------------------------------------------------------
| Offer Folder Presence Channel
|--------------------------------------------------------------------------
| IMPORTANT:
| Register this channel ONLY ONCE.
| It must return an ARRAY, not true/false, because Echo presence needs this
| payload to show the user's name and avatar.
*/
Broadcast::channel('offer-folder.{folderId}', function ($user, int $folderId) {
    $folderExists = OfferFolder::query()->whereKey($folderId)->exists();

    if (!$user || !$folderExists) {
        return false;
    }

    return broadcast_employee_payload($user);
});

// Channel for the Offer Copy and Paste
Broadcast::channel('user.clipboard.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel for customer notes
Broadcast::channel('customer-notes.{customerId}.{alternativeId}.{listId}', function ($user, int $customerId, int $alternativeId, $listId) {
    if (!Auth::check()) {
        return false;
    }

    return LeadAlternativeAdd::query()
        ->where('id', $alternativeId)
        ->where(function ($query) use ($customerId) {
            // Some projects use lead_id, older code may use customer_id.
            $query->where('lead_id', $customerId);

            if (\Illuminate\Support\Facades\Schema::hasColumn('lead_alternative_adds', 'customer_id')) {
                $query->orWhere('customer_id', $customerId);
            }
        })
        ->exists();
});

Broadcast::channel('company-activities', function ($user) {
    return Auth::check();
});

Broadcast::channel('lead-emails', function ($user) {
    return Auth::check();
});
