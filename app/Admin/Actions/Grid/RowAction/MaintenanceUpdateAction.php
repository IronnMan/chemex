<?php

namespace App\Admin\Actions\Grid\RowAction;

use App\Admin\Forms\MaintenanceUpdateForm;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Widgets\Modal;

class MaintenanceUpdateAction extends RowAction
{
    protected $title = '🧱 修复故障';

    /**
     * 渲染模态框.
     * @return Modal|string
     */
    public function render()
    {
        // 实例化表单类并传递自定义参数
        $form = MaintenanceUpdateForm::make()->payload(['id' => $this->getKey()]);

        return Modal::make()
            ->lg()
            ->title('处理物资故障')
            ->body($form)
            ->button($this->title);
    }
}
