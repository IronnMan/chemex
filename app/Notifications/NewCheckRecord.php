<?php

namespace App\Notifications;

use App\Models\CheckRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCheckRecord extends Notification
{
    use Queueable;

    public $check_record;

    /**
     * Create a new notification instance.
     *
     * @param CheckRecord $checkRecord
     */
    public function __construct(CheckRecord $checkRecord)
    {
        $this->check_record = $checkRecord;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via(): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray(): array
    {
        switch ($this->check_record->check_item) {
            case 'part':
                $item_type = '配件';
                break;
            case 'software':
                $item_type = '软件';
                break;
            default:
                $item_type = '设备';
        }

        return [
            'check_record_id' => $this->check_record->id,
            'title' => '你有新的盘点任务',
            'content' => '一份'.$item_type.'盘点任务已经交由你负责。',
            'expired' => $this->check_record->end_time,
            'url' => route('check.records.show', $this->check_record->id),
        ];
    }
}
