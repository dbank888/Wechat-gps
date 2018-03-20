<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    // 可填充字段
    protected $fillable = [
        'user_id', 'code_id', 'ip', 'user_agent',
    ];
}
