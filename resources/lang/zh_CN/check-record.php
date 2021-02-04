<?php

return [
    'labels' => [
        'CheckRecord' => '盘点',
        'User' => '负责人',
        'records' => '盘点任务',
    ],
    'fields' => [
        'check_item' => '盘点项目',
        'start_time' => '开始时间',
        'end_time' => '结束时间',
        'user' => [
            'name' => '负责人',
        ],
        'checker' => [
            'name' => '盘点人',
        ],
        'check_id' => '任务ID',
        'item_id' => '物件',
        'status' => '状态',
        'creator' => '创建者',
        'check_all_counts' => '盘点总数',
        'check_yes_counts' => '盘盈数量',
        'check_no_counts' => '盘亏数量',
        'check_left_counts' => '未盘数量',
    ],
    'options' => [
    ],
];
