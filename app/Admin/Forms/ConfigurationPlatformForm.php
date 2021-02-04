<?php

namespace App\Admin\Forms;

use Dcat\Admin\Widgets\Form;

class ConfigurationPlatformForm extends Form
{
    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        admin_setting($input);

        return $this
            ->response()
            ->success('平台配置更新成功！')
            ->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->switch('switch_to_select_create')
            ->help('启用后，所有记录创建时的下拉选项框，将支持在当前页面快速创建新选项。开启此项前提必须已启用【快速创建选项】的扩展字段。')
            ->default(admin_setting('switch_to_select_create'));
    }
}
