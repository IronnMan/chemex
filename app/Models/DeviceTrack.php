<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(string $key, string $value)
 * @property int device_id
 * @property int staff_id
 */
class DeviceTrack extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'device_tracks';

    /**
     * 设备追踪有一个设备记录.
     * @return HasOne
     */
    public function device(): HasOne
    {
        return $this->hasOne(DeviceRecord::class, 'id', 'device_id');
    }

    /**
     * 设备追踪有一个使用者（雇员）.
     * @return HasOne
     */
    public function staff(): HasOne
    {
        return $this->hasOne(StaffRecord::class, 'id', 'staff_id');
    }
}
