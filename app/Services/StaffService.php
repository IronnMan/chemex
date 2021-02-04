<?php

namespace App\Services;

use App\Models\DeviceTrack;
use App\Models\StaffRecord;

class StaffService
{
    /**
     * 删除雇员.
     * @param $staff_id
     */
    public static function deleteStaff($staff_id)
    {
        $staff = StaffRecord::where('id', $staff_id)->first();
        $staff_tracks = DeviceTrack::where('staff_id', $staff_id)
            ->get();

        foreach ($staff_tracks as $staff_track) {
            $staff_track->delete();
        }

        $staff->delete();
    }
}
