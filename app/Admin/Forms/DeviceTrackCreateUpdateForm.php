<?php

namespace App\Admin\Forms;

use App\Models\DeviceRecord;
use App\Models\DeviceTrack;
use App\Models\StaffRecord;
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
class DeviceTrackCreateUpdateForm extends Form implements LazyRenderable
{
    use LazyWidget;

    /**
     * 处理表单提交逻辑.
     * @param array $input
     * @return JsonResponse
     */
    public function handle(array $input): JsonResponse
    {
        if (! Admin::user()->can('device.track.create_update')) {
            return $this->response()
                ->error('你没有权限执行此操作！')
                ->refresh();
        }

        // 获取设备id
        $device_id = $this->payload['id'] ?? null;

        // 获取雇员id，来自表单传参
        $staff_id = $input['staff_id'] ?? null;

        // 如果没有设备id或者雇员id则返回错误
        if (! $device_id || ! $staff_id) {
            return $this->response()
                ->error('参数错误');
        }

        // 设备记录
        $device = DeviceRecord::where('id', $device_id)->first();
        // 如果没有找到这个设备记录则返回错误
        if (! $device) {
            return $this->response()
                ->error('设备不存在');
        }

        // 雇员记录
        $staff = StaffRecord::where('id', $staff_id)->first();
        // 如果没有找到这个雇员记录则返回错误
        if (! $staff) {
            return $this->response()
                ->error('雇员不存在');
        }

        // 设备追踪
        $device_track = DeviceTrack::where('device_id', $device_id)
            ->first();

        // 如果设备追踪非空，则删除旧追踪，为了留下流水记录
        if (! empty($device_track)) {
            // 如果新使用者和旧使用者相同，返回错误
            if ($device_track->staff_id == $staff_id) {
                return $this->response()
                    ->error('使用者没有改变，无需重新分配');
            } else {
                $device_track->delete();
            }
        }

        // 创建新的设备追踪
        $device_track = new DeviceTrack();
        $device_track->device_id = $device_id;
        $device_track->staff_id = $staff_id;
        $device_track->save();

        return $this->response()
            ->success('使用者分配成功')
            ->refresh();
    }

    /**
     * 构造表单.
     */
    public function form()
    {
        if (Support::ifSelectCreate()) {
            $this->selectCreate('staff_id', '新使用者')
                ->options(StaffRecord::class)
                ->ajax(route('selection.staff.records'))
                ->url(route('staff.records.create'))
                ->help('选择新使用者后，将会自动解除此设备与老使用者的归属关系。')
                ->required();
        } else {
            $this->select('staff_id', '新使用者')
                ->options(StaffRecord::pluck('name', 'id'))
                ->help('选择新使用者后，将会自动解除此设备与老使用者的归属关系。')
                ->required();
        }
    }
}
