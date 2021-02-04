<?php

namespace App\Admin\Actions\Grid\RowAction;

use App\Admin\Forms\PartTrackCreateUpdateForm;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Widgets\Modal;

class PartTrackCreateUpdateAction extends RowAction
{
    protected $title = '💻 归属设备';

    /**
     * 渲染模态框.
     * @return Modal|string
     */
    public function render()
    {
        if (! Admin::user()->can('part.track.create_update')) {
            return '你没有权限执行此操作！';
        }

        // 实例化表单类并传递自定义参数
        $form = PartTrackCreateUpdateForm::make()->payload(['id' => $this->getKey()]);

        return Modal::make()
            ->lg()
            ->title('将 '.$this->getRow()->name.' 归属到设备')
            ->body($form)
            ->button($this->title);
    }
}
