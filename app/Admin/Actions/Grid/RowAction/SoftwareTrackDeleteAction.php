<?php

namespace App\Admin\Actions\Grid\RowAction;

use App\Models\SoftwareTrack;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid\RowAction;

class SoftwareTrackDeleteAction extends RowAction
{
    protected $title = '🔗 解除归属';

    /**
     * 处理动作逻辑.
     * @return Response
     */
    public function handle(): Response
    {
        if (! Admin::user()->can('software.track.delete')) {
            return $this->response()
                ->error('你没有权限执行此操作！')
                ->refresh();
        }

        $software_track = SoftwareTrack::where('id', $this->getKey())->first();

        if (empty($software_track)) {
            return $this->response()
                ->error('找不到此软件归属记录！');
        }

        $software_track->delete();

        return $this->response()
            ->success('软件归属解除成功！')
            ->refresh();
    }

    /**
     * 对话框.
     * @return string[]
     */
    public function confirm(): array
    {
        return ['确认解除与此设备的关联？', '解除后相应的授权数量等将会同步更新'];
    }
}
