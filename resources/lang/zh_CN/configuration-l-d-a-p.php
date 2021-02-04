<?php

return [
    'labels' => [
        'LDAP' => '平台配置',
        'ldap' => 'LDAP',
    ],
    'fields' => [
        'ad_enabled' => '启用状态',
        'ad_host_primary' => '主域控地址',
        'ad_host_secondary' => '备域控地址',
        'ad_port_primary' => '主域控端口',
        'ad_port_secondary' => '备域控端口',
        'ad_base_dn' => '域控根名',
        'ad_username' => '域控验证用户',
        'ad_password' => '域控验证密码',
        'ad_use_ssl' => '启用SSL',
        'ad_use_tls' => '启用TLS',
        'ad_login' => '启用域登录',
        'ad_bind_administrator' => '域管理员的账户',
    ],
    'options' => [
    ],
];
