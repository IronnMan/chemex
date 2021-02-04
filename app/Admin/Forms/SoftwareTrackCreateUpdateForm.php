<?php

namespace App\Admin\Forms;

use App\Models\DeviceRecord;
use App\Models\SoftwareRecord;
use App\Models\SoftwareTrack;
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
class SoftwareTrackCreateUpdateForm extends Form implements LazyRenderable
{
    use LazyWidget;

    /**
     * 处理表单提交逻辑.
     * @param array $input
     * @return JsonResponse
     */
    public function handle(array $input): JsonResponse
    {
        if (! Admin::user()->can('software.track.create_update')) {
            return $this->response()
                ->error('你没有权限执行此操作！')
                ->refresh();
        }

        // 获取软件id
        $software_id = $this->payload['id'] ?? null;

        // 获取设备id，来自表单传参
        $device_id = $input['device_id'] ?? null;

        // 如果没有软件id或者设备id则返回错误
        if (! $software_id || ! $device_id) {
            return $this->response()
                ->error('参数错误');
        }

        // 软件记录
        $software = SoftwareRecord::where('id', $software_id)->first();
        // 如果没有找到这个软件记录则返回错误
        if (! $software) {
            return $this->response()
                ->error('软件不存在');
        }

        // 设备记录
        $device = DeviceRecord::where('id', $device_id)->first();
        // 如果没有找到这个设备记录则返回错误
        if (! $device) {
            return $this->response()
                ->error('设备不存在');
        }

        // 软件追踪
        $software_track = SoftwareTrack::where('software_id', $software_id)
            ->first();

        // 如果软件授权数量为非无限制
        if ($software->counts != -1) {
            $software_tracks = SoftwareTrack::where('software_id', $software_id)
                ->get();
            $used = count($software_tracks);
            $diff = $software->counts - $used;
            if ($diff <= 0) {
                return $this->response()
                    ->error('软件可用授权数量不足，无法归属');
            }
        }

        // 如果软件追踪非空，则删除旧追踪，为了留下流水记录
        if (! empty($software_track)) {
            // 如果新设备和旧设备相同，返回错误
            if ($software_track->device_id == $device_id) {
                return $this->response()
                    ->error('设备没有改变，无需重新归属');
            }
        }

        // 创建新的配件追踪
        $software_track = new SoftwareTrack();
        $software_track->software_id = $software_id;
        $software_track->device_id = $device_id;
        $software_track->save();

        return $this->response()
            ->success('软件归属成功')
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
                ->url(route('device.records.create'));
        } else {
            $this->select('device_id', '新设备')
                ->options(DeviceRecord::all()->pluck('name', 'id'))
                ->help('选择新设备后，将会自动解除此软件与老设备的归属关系。')
                ->required();
        }
    }
}
