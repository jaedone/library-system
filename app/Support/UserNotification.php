<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserNotification
{
    public static function send(
        int $userId,
        string $title,
        string $message,
        string $type = 'service-request',
        ?string $actionUrl = null,
        array $data = []
    ): int {
        return DB::table('user_notifications')->insertGetId([
            'user_id' => $userId,
            'notification_key' => Str::uuid()->toString(),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'data' => empty($data) ? null : json_encode($data),
            'is_read' => false,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}