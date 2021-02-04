<?php

namespace App\Admin\Actions\Grid\RowAction;

use App\Admin\Forms\ServiceIssueForm;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Widgets\Modal;

class ServiceIssueCreateAction extends RowAction
{
    protected $title = '❓ 报告故障';

    /**
     * 渲染模态框.
     * @return Modal|string
     */
    public function render()
    {
        if (! Admin::user()->can('service.issue.create')) {
            return '你没有权限执行此操作！';
        }

        // 实例化表单类并传递自定义参数
        $form = ServiceIssueForm::make()->payload(['id' => $this->getKey()]);

        return Modal::make()
            ->lg()
            ->title('报告 '.$this->getRow()->name.' 发生的故障')
            ->body($form)
            ->button($this->title);
    }
}
