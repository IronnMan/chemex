<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Support;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Card;

class ToolQRCodeGeneratorController extends Controller
{
    /**
     * 页面.
     * @param Content $content
     * @return Content
     */
    public function index(Content $content): Content
    {
        return $content
            ->header('二维码批量生成工具')
            ->description('用于批量生成设备、配件、软件的二维码图片文件')
            ->body(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->row(new Card('第一步', view('tool_qrcode_generator.download')));
                    $data = Support::getAllItemTypeAndId();
                    $column->row(new Card('第二步', view('tool_qrcode_generator.data')->with('data', $data)));
                    $column->row(new Card('第三步', view('tool_qrcode_generator.run')));
                });
            });
    }
}
