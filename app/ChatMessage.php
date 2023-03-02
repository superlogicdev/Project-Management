<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
	protected $table = 'chat_messages';
	
    protected $fillable = [
        'user_id', 'me_id', 'chat_mes_text', 'chat_mes_file', 'chat_mes_img', 'is_delete', 'is_seen', 'is_me_id', 'is_group', 'chat_datetime'
    ];
}
