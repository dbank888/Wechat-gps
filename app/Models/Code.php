<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    // 可填充字段
    protected $fillable = [
        'user_id', 'type', 'code', 'used_at', 'expired_at',
    ];

    public $appends = [
        'type_name'
    ];

    /**
     * 获取类型别名
     *
     * @param $value
     */
    public function getTypeNameAttribute()
    {
        $type = [
          'day' => '天卡',
          'week' => '周卡',
          'month' => '月卡',
          'year' => '年卡',
        ];
        return $this->type_name = $type[$this->type];
    }
}
