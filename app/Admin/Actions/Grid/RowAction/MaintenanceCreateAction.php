<?php

namespace App\Admin\Actions\Grid\RowAction;

use App\Admin\Forms\MaintenanceCreateForm;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Widgets\Modal;

class MaintenanceCreateAction extends RowAction
{
    protected $item = null;

    protected $title = '🔧 报告故障';

    public function __construct($item)
    {
        $this->item = $item;

        parent::__construct();
    }

    /**
     * 渲染模态框.
     * @return Modal|string
     */
    public function render()
    {
        // 实例化表单类并传递自定义参数
        $form = MaintenanceCreateForm::make()->payload([
            'item' => $this->item,
            'item_id' => $this->getKey(),
        ]);

        return Modal::make()
            ->lg()
            ->title('报告 '.$this->getRow()->name.' 发生的故障')
            ->body($form)
            ->button($this->title);
    }
}
