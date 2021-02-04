<?php

return [
    'labels' => [
        'PartRecord' => '配件',
        'records' => '配件',
        'Category Id' => '分类',
        'Vendor Id' => '厂商',
        'Purchased Channel Id' => '购入途径',
        'Depreciation Rule Id' => '折旧规则',
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
        'device' => [
            'name' => '所属设备',
        ],
        'specification' => '规格',
        'sn' => '序列号',
        'price' => '价格',
        'purchased' => '购入日期',
        'expired' => '过保日期',
        'depreciation' => [
            'name' => '折旧规则',
            'termination' => '报废日期',
        ],
        'asset_number' => '资产编号',
        'location' => '位置',
        'expiration_left_days' => '剩余保固时间',
    ],
];
