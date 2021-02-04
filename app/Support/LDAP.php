<?php

namespace App\Support;

use Adldap\Auth\BindException;
use Adldap\Auth\PasswordRequiredException;
use Adldap\Auth\UsernameRequiredException;
use Adldap\Laravel\Facades\Adldap;

class LDAP
{
    /**
     * 用户登录.
     * @param null $username
     * @param null $password
     * @return bool
     * @throws BindException
     * @throws PasswordRequiredException
     * @throws UsernameRequiredException
     */
    public static function auth($username = null, $password = null): bool
    {
        $username = $username == null ? admin_setting('ad_username') : $username;
        $password = $password == null ? admin_setting('ad_password') : $password;

        return Adldap::auth()->attempt($username, $password);
    }
}
