<?php

namespace App\Admin\Actions\Grid\RowAction;

use App\Models\CheckRecord;
use App\Models\CheckTrack;
use App\Services\NotificationService;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid\RowAction;

class CheckRecordUpdateNoAction extends RowAction
{
    protected $title = '❌ 取消盘点任务';

    /**
     * 处理动作逻辑.
     * @return Response
     */
    public function handle(): Response
    {
        if (! Admin::user()->can('check.record.update.no')) {
            return $this->response()
                ->error('你没有权限执行此操作！')
                ->refresh();
        }

        $check_tracks = CheckTrack::where('check_id', $this->getKey())->get();
        foreach ($check_tracks as $check_track) {
            $check_track->delete();
        }
        $check_record = CheckRecord::where('id', $this->getKey())->firstOrFail();
        if ($check_record->status == 1) {
            return $this->response()
                ->warning('失败，此项盘点任务已经完成了。');
        }
        if ($check_record->status == 2) {
            return $this->response()
                ->warning('失败，此项盘点任务已经取消过了。');
        }

        NotificationService::deleteNotificationWhenCheckFinishedOrCancelled($this->getKey());

        $check_record->status = 2;
        $check_record->save();

        return $this->response()
            ->success('盘点任务已经取消！')
            ->refresh();
    }

    /**
     * 对话框.
     * @return string[]
     */
    public function confirm(): array
    {
        return ['取消此盘点任务？', '取消后，相应的盘点追踪将全部被移除。'];
    }
}
