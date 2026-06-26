<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\OfferFolder;
use App\Models\Employee;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The `Broadcast::routes` call will register the
| `/broadcasting/auth` route under the given middleware.
|
*/

// Register the auth route for private & presence channels
Broadcast::routes([
    'middleware' => ['web', 'auth'],  // ensures laravel_session & auth user
]);

// 1:1 chat channel between two users
Broadcast::channel('chat.{a}.{b}', function ($user, $a, $b) {
    return (int) $user->id === (int) $a || (int) $user->id === (int) $b;
});

// User‑specific channel for direct notifications
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
    $employeeId = is_numeric($user->name) ? (int) $user->name : null;

    $employee = $employeeId
        ? Employee::find($employeeId)
        : null;

    $displayName = null;

    if ($employee) {
        $displayName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));

        if (!$displayName && !empty($employee->display_name)) {
            $displayName = $employee->display_name;
        }
    }

    if (!$displayName) {
        $displayName = $user->email ?? ('User #' . $user->id);
    }

    $image = null;

    if ($employee && !empty($employee->image)) {
        $image = asset('images/employee/' . $employee->image);
    } elseif (!empty($user->image)) {
        $image = asset('images/user/' . $user->image);
    } else {
        $image = 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=74b2d4&color=fff';
    }

    return [
        'id' => (int) $user->id,                 // users.id, important for Echo presence
        'employee_id' => $employeeId,            // employees.id
        'name' => $displayName,
        'image' => $image,
    ];
});

 

Broadcast::channel('notifications.user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('ids', function () {
    return true; // PUBLIC REVERB CHANNEL
});



Broadcast::channel('planner.plan.{planId}', function ($user, $planId) {
    // TODO: Replace with your ACL (account_id, permissions, etc.)
    return DB::table('planner_plans')->where('id', (int)$planId)->whereNull('deleted_at')->exists();
});

Broadcast::channel('planner.employee.{employeeId}', function ($user, $employeeId) {
    // If your users table has employee_id:
    if (isset($user->employee_id) && (int)$user->employee_id === (int)$employeeId) return true;

    // Or admin permission fallback:
    return method_exists($user, 'can') ? $user->can('planner-admin') : false;
});

Broadcast::channel('planner.account.{accountId}', function ($user, $accountId) {
    return isset($user->account_id) && (int)$user->account_id === (int)$accountId;
});

Broadcast::channel('offer-folder.{folderId}', function ($user, $folderId) {
    $folder = OfferFolder::find($folderId);

    if (!$folder) {
        return false;
    }

    $employeeId = is_numeric($user->name) ? (int) $user->name : null;
    $employee = $employeeId ? Employee::find($employeeId) : null;

    return [
        'id' => (int) $user->id,
        'name' => $employee
            ? trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''))
            : ('User #' . $user->id),
        'avatar' => ($employee && $employee->image)
            ? asset('images/employee/' . $employee->image)
            : asset('images/gender/male.png'),
    ];
});


Broadcast::channel('user.clipboard.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('employee-appointment.{employeeId}', function ($user, $employeeId) {
    return is_numeric($user->name)
        && (int) $user->name === (int) $employeeId;
});

Broadcast::channel('lead-sidebar-counts', function () {
    return true;
});

Broadcast::channel('employee.{employeeId}', function ($user, $employeeId) {
    if ((string) $user->name === (string) $employeeId) {
        return true;
    }

    // Optional admin access
    return DB::table('user_rolls')
        ->where('user_id', $user->id)
        ->where('item_id', 'Administrator')
        ->where('is_read', 'on')
        ->exists();
});

 
Broadcast::channel('employee.{employeeId}.tasks', function ($user, $employeeId) {
    return (string) $user->name === (string) $employeeId;
});

Broadcast::channel('general-tasks', function ($user) {
    return auth()->check();
});