<?php

namespace App\Admin\Actions\Grid\ToolAction;

use App\Admin\Forms\TodoRecordCreateForm;
use Dcat\Admin\Grid\Tools\AbstractTool;
use Dcat\Admin\Widgets\Modal;

class TodoRecordCreateAction extends AbstractTool
{
    protected $title = '创建待办';

    /**
     * 渲染模态框.
     * @return Modal|string
     */
    public function render()
    {
        return Modal::make()
            ->lg()
            ->body(new TodoRecordCreateForm())
            ->button("<a class='btn btn-success' style='color: white;'><i class='feather icon-plus'></i>&nbsp;$this->title</a>");
    }
}
