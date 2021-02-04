<?php

namespace App\Admin\Actions\Grid\RowAction;

use App\Models\ServiceRecord;
use App\Models\ServiceTrack;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid\RowAction;

class ServiceRecordDeleteAction extends RowAction
{
    protected $title = '🔨 删除服务';

    /**
     * 处理动作逻辑.
     * @return Response
     */
    public function handle(): Response
    {
        if (! Admin::user()->can('service.record.delete')) {
            return $this->response()
                ->error('你没有权限执行此操作！')
                ->refresh();
        }

        $service = ServiceRecord::where('id', $this->getKey())->first();
        if (empty($service)) {
            return $this->response()
                ->error('没有此服务记录！');
        }

        $service_tracks = ServiceTrack::where('service_id', $service->id)
            ->get();

        foreach ($service_tracks as $service_track) {
            $service_track->delete();
        }

        $service->delete();

        return $this->response()
            ->success('成功删除服务: '.$service->name)
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
