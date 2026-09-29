<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\V1\NotificationResource;
use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseApiController
{
    /**
     * Get Notifications List
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = AppNotification::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        $unreadCount = AppNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return $this->success([
            'unread_count' => $unreadCount,
            'notifications' => NotificationResource::collection($notifications),
        ], 'Success', 200, [
            'current_page' => $notifications->currentPage(),
            'last_page' => $notifications->lastPage(),
            'total' => $notifications->total(),
        ]);
    }

    /**
     * Mark Notification as Read
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $notification = AppNotification::where('user_id', $user->id)->find($id);

        if (!$notification) {
            return $this->error($this->getLocale() === 'en' ? 'Notification not found' : 'الإشعار غير موجود', 404);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $this->success(null, 'Marked as read');
    }

    /**
     * Mark All Notifications as Read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        AppNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return $this->success(null, 'All marked as read');
    }
}
