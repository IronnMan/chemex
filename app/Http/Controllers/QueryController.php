<?php

namespace App\Http\Controllers;

use App\Support\Info;
use Illuminate\Http\JsonResponse;
use Pour\Base\Uni;

class QueryController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    /**
     * 移动端扫码查看设备配件软件详情.
     * @param $string
     * @return JsonResponse
     */
    public function query($string): JsonResponse
    {
        $item = explode(':', $string)[0];
        $id = explode(':', $string)[1];
        $item = Info::getItemRecordByClass($item, $id);
        optional($item->staff)->department;
        $item->category;
        $item->vendor;
        $item->channel;
        $return = Uni::rr(200, '查询成功', $item);

        return response()->json($return);
    }
}
