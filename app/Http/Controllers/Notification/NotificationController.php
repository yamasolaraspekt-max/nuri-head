<?php

namespace App\Http\Controllers\Notification;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(function ($n) {
                $data = $n->data ?? [];
                $type = $data['type'] ?? 'info';

                // scope: for choosing icons
                $scope = 'generic';
                if (!empty($data['customer_id']) || !empty($data['lead_id'])) {
                    $scope = 'customer';
                } elseif (!empty($data['emp_id']) || !empty($data['employee_id'])) {
                    $scope = 'employee';
                } elseif (!empty($data['project_id'])) {
                    $scope = 'project';
                } elseif (!empty($data['ticket_id'])) {
                    $scope = 'ticket';
                } elseif (!empty($data['task_id'])) {
                    $scope = 'task';
                }

                $performedAt = $data['performed_at'] ?? optional($n->created_at)->toDateTimeString();

                return [
                    'id'               => $n->id,
                    'type'             => $type,
                    'scope'            => $scope,
                    'title'            => $data['title'] ?? 'Benachrichtigung',
                    'message'          => $data['message'] ?? '',
                    'performed_at'     => $performedAt,
                    'performed_at_human' => optional($n->created_at)->diffForHumans(),
                    'is_read'          => !is_null($n->read_at),
                ];
            })
            ->values();

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['status' => 'ok']);
    }
}
