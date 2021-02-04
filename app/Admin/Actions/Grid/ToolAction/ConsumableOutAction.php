<?php

namespace App\Admin\Actions\Grid\ToolAction;

use App\Admin\Forms\ConsumableOutForm;
use Dcat\Admin\Grid\Tools\AbstractTool;
use Dcat\Admin\Widgets\Modal;

class ConsumableOutAction extends AbstractTool
{
    protected $title = '领用';

    /**
     * 渲染模态框.
     * @return Modal|string
     */
    public function render()
    {
        return Modal::make()
            ->lg()
            ->body(new ConsumableOutForm())
            ->button("<a class='btn btn-warning' style='color: white;'><i class='feather icon-package'></i>&nbsp;$this->title</a>");
    }
}
