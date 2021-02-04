<?php

namespace App\Admin\Forms;

use App\Models\CheckTrack;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class CheckTrackUpdateForm extends Form implements LazyRenderable
{
    use LazyWidget;

    /**
     * 处理表单提交逻辑.
     * @param array $input
     * @return JsonResponse
     */
    public function handle(array $input): JsonResponse
    {
        if (! Admin::user()->can('check.track.update')) {
            return $this->response()
                ->error('你没有权限执行此操作！')
                ->refresh();
        }

        // 获取盘点id
        $track_id = $this->payload['id'] ?? null;

        // 获取盘点状态
        $status = $input['status'] ?? null;

        // 获取盘点说明
        $description = $input['description'] ?? null;

        // 如果没有盘点id返回错误
        if (! $track_id || ! $status) {
            return $this->response()
                ->error('参数错误');
        }

        $check_track = CheckTrack::where('id', $track_id)->first();
        if (empty($check_track)) {
            return $this->response()
                ->error('没有找到此盘点追踪');
        } else {
            $check_track->status = $status;
            $check_track->description = $description;
            $check_track->checker = Admin::user()->id;
            $check_track->save();
        }

        return $this->response()
            ->success('盘点操作成功')
            ->refresh();
    }

    /**
     * 构造表单.
     */
    public function form()
    {
        $this->radio('status', '盘点状态')
            ->options([1 => '盘到啦', 2 => '没盘到'])
            ->default(1)
            ->required();
        $this->textarea('description', '描述');
    }
}
