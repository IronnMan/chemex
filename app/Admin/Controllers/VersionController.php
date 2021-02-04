<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ConfigService;
use App\Services\VersionService;
use App\Support\Version;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Card;
use Illuminate\Http\JsonResponse;

class VersionController extends Controller
{
    /**
     * 页面.
     * @param Content $content
     * @return Content
     */
    public function index(Content $content): Content
    {
        $version = config('admin.chemex_version');
        $description = Version::list()['yirgacheffe'];

        return $content
            ->header('版本')
            ->description('列出了与咖啡壶相关的版本信息')
            ->body(function (Row $row) use ($version, $description) {
                $row->column(3, function (Column $column) use ($version) {
                    $column->row(new Card('当前版本', $version));
                });
                $row->column(9, function (Column $column) use ($description) {
                    $column->row(new Card($description['name'], $description['description']));
                });
            });
    }

    /**
     * 更新数据库结构.
     * @return JsonResponse
     */
    public function migrate(): JsonResponse
    {
        $result = ConfigService::migrate();
        if ($result) {
            $return = response()->json([
                'code' => 200,
                'message' => '更新数据库结构成功',
            ]);
        } else {
            $return = response()->json([
                'code' => 500,
                'message' => '更新数据库结构失败',
            ]);
        }

        return response()->json($return);
    }

    /**
     * 清理全部缓存.
     * @return JsonResponse
     */
    public function clear(): JsonResponse
    {
        $result = ConfigService::clear();
        if ($result) {
            $return = response()->json([
                'code' => 200,
                'message' => '缓存清理成功',
            ]);
        } else {
            $return = response()->json([
                'code' => 500,
                'message' => '缓存清理失败',
            ]);
        }

        return response()->json($return);
    }

    /**
     * 获取远程版本号.
     * @return string|null
     */
    public function getRemoteVersion(): ?string
    {
        return VersionService::getLatestVersionFromGitee();
    }
}
