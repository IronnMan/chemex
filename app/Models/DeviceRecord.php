<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

/**
 * @method static where(string $key, string $value, string $value = null)
 * @method static whereBetween(string $string, array $array)
 * @method static count()
 * @property string name
 * @property string description
 * @property int category_id
 * @property int vendor_id
 * @property string sn
 * @property string mac
 * @property string ip
 * @property float price
 * @property string purchased
 * @property string expired
 * @property int purchased_channel_id
 * @property string security_password
 * @property string admin_password
 * @property string asset_number
 * @property int id
 */
class DeviceRecord extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'device_records';

    /**
     * 设备记录有一个分类.
     * @return HasOne
     */
    public function category(): HasOne
    {
        return $this->hasOne(DeviceCategory::class, 'id', 'category_id');
    }

    /**
     * 设备记录有一个厂商.
     * @return HasOne
     */
    public function vendor(): HasOne
    {
        return $this->hasOne(VendorRecord::class, 'id', 'vendor_id');
    }

    /**
     * 设备记录有一个购入途径.
     * @return HasOne
     */
    public function channel(): HasOne
    {
        return $this->hasOne(PurchasedChannel::class, 'id', 'purchased_channel_id');
    }

    /**
     * 设备记录在远处有很多配件.
     * @return HasManyThrough
     */
    public function part(): HasManyThrough
    {
        return $this->hasManyThrough(
            'Celaraze\\Chemex\\Part\\Models\\PartRecord',  // 远程表
            'Celaraze\\Chemex\\Part\\Models\\PartTrack',   // 中间表
            'device_id',    // 中间表对主表的关联字段
            'id',   // 远程表对中间表的关联字段
            'id',   // 主表对中间表的关联字段
            'part_id'); // 中间表对远程表的关联字段
    }

    /**
     * 设备记录在远处有很多软件.
     * @return HasManyThrough
     */
    public function software(): HasManyThrough
    {
        return $this->hasManyThrough(
            'Celaraze\\Chemex\\Software\\Models\\SoftwareRecord',  // 远程表
            'Celaraze\\Chemex\\Software\\Models\\SoftwareTrack',  // 中间表
            'device_id',    // 中间表对主表的关联字段
            'id',   // 远程表对中间表的关联字段
            'id',   // 主表对中间表的关联字段
            'software_id'); // 中间表对远程表的关联字段
    }

    /**
     * 设备记录在远处有很多服务程序.
     * @return HasManyThrough
     */
    public function service(): HasManyThrough
    {
        return $this->hasManyThrough(
            'Celaraze\\Chemex\\Service\\Models\\ServiceRecord',  // 远程表
            'Celaraze\\Chemex\\Service\\Models\\ServiceTrack',  // 中间表
            'device_id',    // 中间表对主表的关联字段
            'id',   // 远程表对中间表的关联字段
            'id',   // 主表对中间表的关联字段
            'service_id'); // 中间表对远程表的关联字段
    }

    /**
     * 设备记录在远处有一个使用者（雇员）.
     * @return HasManyThrough
     */
    public function staff(): HasManyThrough
    {
        return $this->hasOneThrough(
            StaffRecord::class,  // 远程表
            DeviceTrack::class,   // 中间表
            'device_id',    // 中间表对主表的关联字段
            'id',   // 远程表对中间表的关联字段
            'id',   // 主表对中间表的关联字段
            'staff_id'); // 中间表对远程表的关联字段
    }

    /**
     * 对安全密码字段读取做解密转换.
     * @param $security_password
     * @return array|string
     */
    public function getSecurityPasswordAttribute($security_password)
    {
        if (! empty($security_password)) {
            try {
                return Crypt::decryptString($security_password);
            } catch (Exception $exception) {
                return '密钥已损毁';
            }
        }
    }

    /**
     * 对安全密码字段写入做加密转换.
     * @param $security_password
     */
    public function setSecurityPasswordAttribute($security_password)
    {
        $this->attributes['security_password'] = Crypt::encryptString($security_password);
    }

    /**
     * 对管理员密码字段读取做解密转换.
     * @param $admin_password
     * @return array|string
     */
    public function getAdminPasswordAttribute($admin_password)
    {
        if (! empty($admin_password)) {
            try {
                return Crypt::decryptString($admin_password);
            } catch (Exception $exception) {
                return '密钥已损毁';
            }
        }
    }

    /**
     * 对管理员密码字段写入做加密转换.
     * @param $admin_password
     */
    public function setAdminPasswordAttribute($admin_password)
    {
        $this->attributes['admin_password'] = Crypt::encryptString($admin_password);
    }

    /**
     * 设备分类有一个折旧规则.
     * @return HasOne
     */
    public function depreciation(): HasOne
    {
        return $this->hasOne(DepreciationRule::class, 'id', 'depreciation_rule_id');
    }
}
