<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(string $key, string $value1, string $value2 = null)
 * @method static truncate()
 * @method static pluck(string $text, string $id)
 * @method static count()
 * @property int department_id
 * @property string name
 * @property string gender
 * @property string|null title
 * @property string|null mobile
 * @property string|null email
 * @property int ad_tag
 */
class StaffRecord extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'staff_records';

    /**
     * 雇员记录有一个组织部门记录.
     * @return HasOne
     */
    public function department(): HasOne
    {
        return $this->hasOne(StaffDepartment::class, 'id', 'department_id');
    }
}
