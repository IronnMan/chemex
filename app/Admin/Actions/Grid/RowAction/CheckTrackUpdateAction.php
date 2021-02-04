<?php

namespace App\Admin\Actions\Grid\RowAction;

use App\Admin\Forms\CheckTrackUpdateForm;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Widgets\Modal;

class CheckTrackUpdateAction extends RowAction
{
    protected $title = '👨‍💼 处理盘点';

    /**
     * 渲染模态框.
     * @return Modal|string
     */
    public function render()
    {
        if (! Admin::user()->can('check.track.update')) {
            return '你没有权限执行此操作！';
        }

        // 实例化表单类并传递自定义参数
        $form = CheckTrackUpdateForm::make()->payload(['id' => $this->getKey()]);

        return Modal::make()
            ->lg()
            ->title('处理盘点')
            ->body($form)
            ->button($this->title);
    }
}
