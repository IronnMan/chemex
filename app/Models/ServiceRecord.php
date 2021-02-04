<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(string $key, string $value)
 */
class ServiceRecord extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'service_records';

    /**
     * 服务程序在远处有一个设备记录.
     * @return HasOneThrough
     */
    public function device(): HasOneThrough
    {
        return $this->hasOneThrough(
            DeviceRecord::class,  // 远程表
            ServiceTrack::class,   // 中间表
            'service_id',    // 中间表对主表的关联字段
            'id',   // 远程表对中间表的关联字段
            'id',   // 主表对中间表的关联字段
            'device_id'); // 中间表对远程表的关联字段
    }
}
