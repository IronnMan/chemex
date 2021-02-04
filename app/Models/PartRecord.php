<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(string $key, string $value, string $value = null)
 * @method static whereBetween(string $string, array $array)
 * @property float price
 * @property string purchased
 * @property string expired
 * @property string specification
 * @property int category_id
 * @property int vendor_id
 * @property string name
 * @property string description
 * @property string sn
 * @property int purchased_channel_id
 * @property string asset_number
 */
class PartRecord extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'part_records';

    /**
     * 配件记录有一个分类.
     * @return HasOne
     */
    public function category(): HasOne
    {
        return $this->hasOne(PartCategory::class, 'id', 'category_id');
    }

    /**
     * 配件记录有一个厂商.
     * @return HasOne
     */
    public function vendor(): HasOne
    {
        return $this->hasOne(VendorRecord::class, 'id', 'vendor_id');
    }

    /**
     * 配件记录有一个购入途径.
     * @return HasOne
     */
    public function channel(): HasOne
    {
        return $this->hasOne(PurchasedChannel::class, 'id', 'purchased_channel_id');
    }

    /**
     * 配件记录在远处有一个设备.
     * @return HasManyThrough
     */
    public function device(): HasManyThrough
    {
        return $this->hasOneThrough(
            DeviceRecord::class,  // 远程表
            PartTrack::class,   // 中间表
            'part_id',    // 中间表对主表的关联字段
            'id',   // 远程表对中间表的关联字段
            'id',   // 主表对中间表的关联字段
            'device_id'); // 中间表对远程表的关联字段
    }

    /**
     * 配件分类有一个折旧规则.
     * @return HasOne
     */
    public function depreciation(): HasOne
    {
        return $this->hasOne(DepreciationRule::class, 'id', 'depreciation_rule_id');
    }
}
