<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @method static where(string $key, string $value)
 * @property int id
 * @property string username
 * @property string password
 * @property string name
 */
class AdminUser extends User implements JWTSubject
{
    use HasFactory;
    use HasDateTimeFormatter;
    use Notifiable;

    protected $table = 'admin_users';
    protected $hidden = ['password'];

    protected static function booted()
    {
        // 保存回调，demo模式下不允许修改管理员信息
        static::saving(function () {
            if (config('admin.demo')) {
                abort(401, '演示模式下不允许修改');
            }
        });
    }

    /**
     * 获取JWT验证器.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * 构造JWT自定义的声明key-values.
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
