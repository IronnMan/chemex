<?php

namespace App\Admin\Actions\Grid\RowAction;

use App\Models\ServiceTrack;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid\RowAction;

class ServiceTrackDeleteAction extends RowAction
{
    protected $title = '🔗 解除归属';

    /**
     * 处理动作逻辑.
     * @return Response
     */
    public function handle(): Response
    {
        if (! Admin::user()->can('service.track.delete')) {
            return $this->response()
                ->error('你没有权限执行此操作！')
                ->refresh();
        }

        $service_track = ServiceTrack::where('id', $this->getKey())->first();

        if (empty($service_track)) {
            return $this->response()
                ->error('找不到此服务归属记录！');
        }

        $service_track->delete();

        return $this->response()
            ->success('服务归属解除成功！')
            ->refresh();
    }

    /**
     * 对话框.
     * @return string[]
     */
    public function confirm(): array
    {
        return ['确认解除与此设备的关联？'];
    }
}
