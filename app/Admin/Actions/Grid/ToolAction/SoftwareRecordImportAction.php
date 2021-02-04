<?php

namespace App\Admin\Actions\Grid\ToolAction;

use App\Admin\Forms\SoftwareRecordImportForm;
use Dcat\Admin\Grid\Tools\AbstractTool;
use Dcat\Admin\Widgets\Modal;

class SoftwareRecordImportAction extends AbstractTool
{
    protected $title = '导入';

    /**
     * 渲染模态框.
     * @return Modal|string
     */
    public function render()
    {
        return Modal::make()
            ->lg()
            ->body(new SoftwareRecordImportForm())
            ->button("<a class='btn btn-success' style='color: white;'><i class='feather icon-package'></i>&nbsp;$this->title</a>");
    }
}
