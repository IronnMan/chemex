<?php

namespace App\Admin\Actions\Grid\BatchAction;

use App\Services\SoftwareService;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid\BatchAction;

class SoftwareRecordBatchDeleteAction extends BatchAction
{
    protected $action;

    protected $title = '🔨 批量删除软件';

    // 确认弹窗信息
    public function confirm(): string
    {
        return '您确定要删除选中的软件吗？';
    }

    // 处理请求
    public function handle(): Response
    {
        if (! Admin::user()->can('software.batch.delete')) {
            return $this->response()
                ->error('你没有权限执行此操作！')
                ->refresh();
        }

        // 获取选中的ID
        $keys = $this->getKey();

        foreach ($keys as $key) {
            SoftwareService::deleteSoftware($key);
        }

        return $this->response()->success('批量删除软件成功！')->refresh();
    }
}
