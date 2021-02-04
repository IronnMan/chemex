<?php

namespace App\Admin\Forms;

use App\Models\DeviceRecord;
use App\Models\PartRecord;
use App\Models\PartTrack;
use App\Support\Support;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

/**
 * 设备记录分配使用者
 * Class DeviceTrackCreateUpdateForm.
 */
class PartTrackCreateUpdateForm extends Form implements LazyRenderable
{
    use LazyWidget;

    /**
     * 处理表单提交逻辑.
     * @param array $input
     * @return JsonResponse
     */
    public function handle(array $input): JsonResponse
    {
        if (! Admin::user()->can('part.track.create_update')) {
            return $this->response()
                ->error('你没有权限执行此操作！')
                ->refresh();
        }

        // 获取配件id
        $part_id = $this->payload['id'] ?? null;

        // 获取设备id，来自表单传参
        $device_id = $input['device_id'] ?? null;

        // 如果没有配件id或者设备id则返回错误
        if (! $part_id || ! $device_id) {
            return $this->response()
                ->error('参数错误');
        }

        // 配件记录
        $part = PartRecord::where('id', $part_id)->first();
        // 如果没有找到这个配件记录则返回错误
        if (! $part) {
            return $this->response()
                ->error('配件不存在');
        }

        // 设备记录
        $device = DeviceRecord::where('id', $device_id)->first();
        // 如果没有找到这个设备记录则返回错误
        if (! $device) {
            return $this->response()
                ->error('设备不存在');
        }

        // 配件追踪
        $part_track = PartTrack::where('part_id', $part_id)
            ->first();

        // 如果配件追踪非空，则删除旧追踪，为了留下流水记录
        if (! empty($part_track)) {
            // 如果新设备和旧设备相同，返回错误
            if ($part_track->device_id == $device_id) {
                return $this->response()
                    ->error('设备没有改变，无需重新归属');
            } else {
                $part_track->delete();
            }
        }

        // 创建新的配件追踪
        $part_track = new PartTrack();
        $part_track->part_id = $part_id;
        $part_track->device_id = $device_id;
        $part_track->save();

        return $this->response()
            ->success('配件归属成功')
            ->refresh();
    }

    /**
     * 构造表单.
     */
    public function form()
    {
        if (Support::ifSelectCreate()) {
            $this->selectCreate('device_id', '新设备')
                ->options(DeviceRecord::class)
                ->ajax(route('selection.device.records'))
                ->url(route('device.records.create'))
                ->help('选择新设备后，将会自动解除此配件与老设备的归属关系。')
                ->required();
        } else {
            $this->select('device_id', '新设备')
                ->options(DeviceRecord::all()->pluck('name', 'id'))
                ->help('选择新设备后，将会自动解除此配件与老设备的归属关系。')
                ->required();
        }
    }
}
