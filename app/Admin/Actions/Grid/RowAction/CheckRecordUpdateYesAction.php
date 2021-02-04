<?php

namespace App\Admin\Actions\Grid\RowAction;

use App\Models\CheckRecord;
use App\Models\CheckTrack;
use App\Services\NotificationService;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid\RowAction;

class CheckRecordUpdateYesAction extends RowAction
{
    protected $title = '⚡ 完成任务';

    public function __construct($title = null)
    {
        $this->title = admin_setting('title');
        parent::__construct($title);
    }

    /**
     * 处理动作逻辑.
     * @return Response
     */
    public function handle(): Response
    {
        if (! Admin::user()->can('check.record.update.yes')) {
            return $this->response()
                ->error('你没有权限执行此操作！')
                ->refresh();
        }

        $check_track = CheckTrack::where('status', 0)->where('check_id', $this->getKey())->first();
        if (empty($check_track)) {
            $check_record = CheckRecord::where('id', $this->getKey())->firstOrFail();
            if ($check_record->status == 1) {
                return $this->response()
                    ->warning('失败，此项盘点任务已经被完成过了。');
            }
            if ($check_record->status == 2) {
                return $this->response()
                    ->warning('失败，此项盘点任务已经被提前中止了。');
            }

            NotificationService::deleteNotificationWhenCheckFinishedOrCancelled($this->getKey());

            $check_record->status = 1;
            $check_record->save();

            return $this->response()
                ->success('太棒了，已经完成了此项盘点全部内容！')
                ->refresh();
        } else {
            return $this->response()
                ->error('失败，至少还有一项未完成的盘点追踪：'.$check_track->id);
        }
    }

    /**
     * 对话框.
     * @return string[]
     */
    public function confirm(): array
    {
        return ['完成盘点任务？', '请确认已经完成了所有相关的盘点追踪工作。'];
    }
}
