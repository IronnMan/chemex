<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Card;

class ToolChemexAppController extends Controller
{
    /**
     * 页面.
     * @param Content $content
     * @return Content
     */
    public function index(Content $content): Content
    {
        return $content
            ->header('Chemex客户端')
            ->description('用于移动端查询、盘点的客户端工具')
            ->body(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->row(new Card('第一步', view('tool_chemex_app.download')));
                });
            });
    }
}
