<?php

namespace App\Admin\Forms;

use App\Models\DeviceRecord;
use App\Models\ServiceRecord;
use App\Models\ServiceTrack;
use App\Support\Support;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class ServiceTrackCreateUpdateForm extends Form implements LazyRenderable
{
    use LazyWidget;

    /**
     * 处理表单提交逻辑.
     * @param array $input
     * @return JsonResponse
     */
    public function handle(array $input): JsonResponse
    {
        if (! Admin::user()->can('service.track.create_update')) {
            return $this->response()
                ->error('你没有权限执行此操作！')
                ->refresh();
        }

        // 获取服务id
        $service_id = $this->payload['id'] ?? null;

        // 获取设备id，来自表单传参
        $device_id = $input['device_id'] ?? null;

        // 如果没有服务id或者设备id则返回错误
        if (! $service_id || ! $device_id) {
            return $this->response()
                ->error('参数错误');
        }

        // 服务记录
        $service = ServiceRecord::where('id', $service_id)->first();
        // 如果没有找到这个服务记录则返回错误
        if (! $service) {
            return $this->response()
                ->error('服务程序不存在');
        }

        // 设备记录
        $device = DeviceRecord::where('id', $device_id)->first();
        // 如果没有找到这个设备记录则返回错误
        if (! $device) {
            return $this->response()
                ->error('设备不存在');
        }

        // 服务追踪
        $service_track = ServiceTrack::where('service_id', $service_id)
            ->first();

        // 如果配件追踪非空，则删除旧追踪，为了留下流水记录
        if (! empty($service_track)) {
            // 如果新设备和旧设备相同，返回错误
            if ($service_track->device_id == $device_id) {
                return $this->response()
                    ->error('设备没有改变，无需重新归属');
            } else {
                $service_track->delete();
            }
        }

        // 创建新的配件追踪
        $service_track = new ServiceTrack();
        $service_track->service_id = $service_id;
        $service_track->device_id = $device_id;
        $service_track->save();

        return $this->response()
            ->success('服务程序归属成功')
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
                ->help('选择新设备后，将会自动解除此服务程序与老设备的归属关系。')
                ->required();
        } else {
            $this->select('device_id', '新设备')
                ->options(DeviceRecord::all()->pluck('name', 'id'))
                ->help('选择新设备后，将会自动解除此服务程序与老设备的归属关系。')
                ->required();
        }
    }
}
