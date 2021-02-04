<?php

namespace App\Admin\Forms;

use App\Models\ConsumableRecord;
use App\Models\ConsumableTrack;
use App\Models\StaffRecord;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Widgets\Form;
use Exception;

class ConsumableOutForm extends Form
{
    /**
     * 处理表单提交逻辑.
     * @param array $input
     * @return JsonResponse
     */
    public function handle(array $input): JsonResponse
    {
        $consumable_record_id = $input['consumable_id'] ?? null;
        $number = $input['number'] ?? null;
        $staff_id = $input['staff_id'] ?? null;
        if (empty($consumable_record_id) || empty($number) || empty($staff_id)) {
            return $this->response()
                ->error('缺少必要的字段！');
        }
        try {
            $consumable_track = ConsumableTrack::where('consumable_id', $consumable_record_id)->first();
            if (empty($consumable_track)) {
                return $this->response()
                    ->error('没有此条记录！');
            } else {
                $new_consumable_track = $consumable_track->replicate();
                $new_consumable_track->number -= $number;
                $new_consumable_track->change = $number;
                $new_consumable_track->staff_id = $staff_id;
                $new_consumable_track->operator = auth('admin')->user()->name;
                $new_consumable_track->save();
                $consumable_track->delete();
            }
            $return = $this->response()
                ->success('成功！')
                ->refresh();
        } catch (Exception $e) {
            $return = $this
                ->response()
                ->error('失败：'.$e->getMessage());
        }

        return $return;
    }

    /**
     * 构造表单.
     */
    public function form()
    {
        $this->select('consumable_id')
            ->options(ConsumableRecord::all()
                ->pluck('name', 'id'))
            ->required();
        $this->currency('number')
            ->symbol('')
            ->required();
        $this->select('staff_id')
            ->options(StaffRecord::all()
                ->pluck('name', 'id'))
            ->required();
    }
}
