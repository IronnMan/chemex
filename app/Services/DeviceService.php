<?php

namespace App\Services;

use App\Models\DeviceRecord;
use App\Models\DeviceTrack;
use App\Models\PartTrack;
use App\Models\SoftwareTrack;
use App\Support\Data;
use App\Support\Support;

/**
 * 和设备记录相关的功能服务
 * Class DeviceService.
 */
class DeviceService
{
    /**
     * 获取设备的软配件内容.
     * @param $id
     * @return mixed
     */
    public static function related($id): array
    {
        $device = DeviceRecord::where('id', $id)
            ->firstOrFail();

        // 获取所有配件
        $part = $device->part;
        // 获取所有软件
        $software = $device->software;
        // 获取所有服务程序
        $service = $device->service;

        // 转换软件授权方式的显示内容
        foreach ($software as $item) {
            $item->distribution = Data::distribution()[$item->distribution];
        }

        $data['part'] = $part;
        $data['software'] = $software;
        $data['service'] = $service;

        return $data;
    }

    /**
     * 获取设备的履历清单.
     * @param $id
     * @return array
     */
    public static function history($id): array
    {
        $data = [];

        $single = [
            'type' => '',
            'name' => '',
            'status' => '',
            'style' => '',
            'datetime' => '',
        ];

        // 处理设备使用者变动履历
        $device_tracks = DeviceTrack::withTrashed()
            ->where('device_id', $id)
            ->get();
        foreach ($device_tracks as $device_track) {
            $single['type'] = '用户';
            $device = $device_track->staff()->withTrashed()->first();
            $single['name'] = $device->name.' - '.$device_track->staff()
                    ->withTrashed()
                    ->first()
                    ->department()
                    ->withTrashed()
                    ->first()
                    ->name;
            $data = Support::itemTrack($single, $device_track, $data);
        }

        // 处理设备配件变动履历
        $part_tracks = PartTrack::withTrashed()
            ->where('device_id', $id)
            ->get();
        foreach ($part_tracks as $part_track) {
            $single['type'] = '配件';
            $part = $part_track->part()->withTrashed()->first();
            $single['name'] = $part->name.' - '.$part->specification;
            $data = Support::itemTrack($single, $part_track, $data);
        }

        // 处理设备软件变动履历
        $software_tracks = SoftwareTrack::withTrashed()
            ->where('device_id', $id)
            ->get();
        foreach ($software_tracks as $software_track) {
            $single['type'] = '软件';
            $software = $software_track->software()->withTrashed()->first();
            $single['name'] = $software->name.' '.$software->version;
            $data = Support::itemTrack($single, $software_track, $data);
        }

        $datetime = array_column($data, 'datetime');
        array_multisort($datetime, SORT_DESC, $data);

        return $data;
    }

    /**
     * 删除设备.
     * @param $device_id
     */
    public static function deviceDelete($device_id)
    {
        $device_record = DeviceRecord::where('id', $device_id)->first();
        if (! empty($device_record)) {
            // 软删除设备归属记录
            $device_tracks = DeviceTrack::where('device_id', $device_id)->get();
            foreach ($device_tracks as $device_track) {
                $device_track->delete();
            }

            // 软删除配件归属记录
            $part_tracks = PartTrack::where('device_id', $device_id)->get();
            foreach ($part_tracks as $part_track) {
                $part_track->delete();
            }

            // 软删除软件归属记录
            $software_tracks = SoftwareTrack::where('device_id', $device_id)->get();
            foreach ($software_tracks as $software_track) {
                $software_track->delete();
            }

            // 软删除服务归属记录
            $service_tracks = SoftwareTrack::where('device_id', $device_id)->get();
            foreach ($service_tracks as $service_track) {
                $service_track->delete();
            }
            $device_record->delete();
        }
    }
}
