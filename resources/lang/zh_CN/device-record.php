<?php

return [
    'labels' => [
        'DeviceRecord' => '设备',
        'Category' => '分类',
        'Vendor' => '厂商',
        'Purchased Channel Id' => '购入途径',
        'Expiration Left Days' => '保固剩余天数',
        'records' => '设备',
        'Depreciation Rule Id' => '折旧规则',
        'Depreciation Price' => '折旧价格',
    ],
    'fields' => [
        'qrcode' => '二维码',
        'name' => '名称',
        'description' => '描述',
        'category' => [
            'name' => '分类',
        ],
        'vendor' => [
            'name' => '厂商',
        ],
        'channel' => [
            'name' => '购入途径',
        ],
        'sn' => '序列号',
        'mac' => 'MAC',
        'ip' => 'IP',
        'photo' => '照片',
        'staff' => [
            'name' => '雇员',
            'department' => [
                'name' => '部门',
            ],
            'department_id' => '部门',
        ],
        'price' => '价格',
        'purchased' => '购入日期',
        'expired' => '过保日期',
        'security_password' => '安全密码',
        'admin_password' => '管理员密码',
        'part' => [
            'category' => [
                'name' => '分类',
            ],
            'name' => '名称',
            'specification' => '规格',
            'sn' => '序列号',
            'vendor' => [
                'name' => '厂商',
            ],
        ],
        'software' => [
            'category' => [
                'name' => '分类',
            ],
            'name' => '名称',
            'version' => '版本',
            'distribution' => '发行方式',
            'vendor' => [
                'name' => '厂商',
            ],
        ],
        'service' => [
            'name' => '名称',
        ],
        'depreciation' => [
            'name' => '折旧规则',
        ],
        'asset_number' => '资产编号',
        'location' => '位置',
        'category_id' => '分类',
        'vendor_id' => '厂商',
        'depreciation_id' => '折旧规则',
    ],
    'options' => [
    ],
];
