<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitrixChat extends Model
{
    protected $fillable = [
        'parent_chat_id',
        'parent_message_id',
        'name',
        'description',
        'owner',
        'extranet',
        'avatar',
        'color',
        'type',
        'counter',
        'user_counter',
        'message_count',
        'unread_id',
        'last_message_id',
        'last_id',
        'marked_id',
        'disk_folder_id',
        'entity_type',
        'entity_id',
        'entity_data_1',
        'entity_data_2',
        'entity_data_3',
        'restrictions',
        'mute_list',
        'date_create',
        'message_type',
        'disappearing_time',
        'public',
        'role',
        'entity_link',
        'permissions',
        'is_new',
        'readed_list',
        'manager_list',
    ];

    protected $casts = [
        'restrictions' => 'array',
        'mute_list' => 'array',
        'entity_link' => 'array',
        'permissions' => 'array',
        'readed_list' => 'array',
        'manager_list' => 'array',
    ];
}
