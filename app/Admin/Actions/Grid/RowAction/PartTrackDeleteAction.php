<?php

namespace App\Admin\Actions\Grid\RowAction;

use App\Models\PartTrack;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid\RowAction;

class PartTrackDeleteAction extends RowAction
{
    protected $title = '🔗 解除归属';

    /**
     * 处理动作逻辑.
     * @return Response
     */
    public function handle(): Response
    {
        if (! Admin::user()->can('part.track.delete')) {
            return $this->response()
                ->error('你没有权限执行此操作！')
                ->refresh();
        }

        $part_track = PartTrack::where('id', $this->getKey())->first();

        if (empty($part_track)) {
            return $this->response()
                ->error('找不到此配件归属记录！');
        }

        $part_track->delete();

        return $this->response()
            ->success('配件归属解除成功！')
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
