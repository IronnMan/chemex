<?php

namespace App\Admin\Actions\Grid\RowAction;

use App\Services\DeviceService;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid\RowAction;

class DeviceRecordDeleteAction extends RowAction
{
    protected $title = '🔨 删除设备';

    /**
     * 处理动作逻辑.
     * @return Response
     */
    public function handle(): Response
    {
        if (! Admin::user()->can('device.record.delete')) {
            return $this->response()
                ->error('你没有权限执行此操作！')
                ->refresh();
        }

        DeviceService::deviceDelete($this->getKey());

        return $this->response()
            ->success('成功删除设备！')
            ->refresh();
    }

    /**
     * 对话框.
     * @return string[]
     */
    public function confirm(): array
    {
        return ['确认删除？', '删除的同时将会解除所有与之关联的归属关系'];
    }
}
