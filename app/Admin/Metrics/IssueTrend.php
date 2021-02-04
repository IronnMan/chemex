<?php

namespace App\Admin\Metrics;

use App\Models\ServiceIssue;
use Dcat\Admin\Widgets\Metrics\Line;
use Illuminate\Http\Request;

class IssueTrend extends Line
{
    /**
     * @var string
     */
    protected $label = '服务异常趋势（每月）';

    /**
     * 图表默认高度.
     *
     * @var int
     */
    protected $chartHeight = 140;

    protected $chartMarginRight = 1;

    /**
     * 处理请求
     *
     * @param Request $request
     *
     * @return mixed|void
     */
    public function handle(Request $request)
    {
        $year = date('Y', time());
        if ($request->get('option') == 'pre_year') {
            $year = (int) $year - 1;
        }
        $from = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year));
        $to = date('Y-m-d', mktime(23, 59, 59, 12, 31, $year));

        $records = ServiceIssue::whereBetween('start', [$from, $to])->get();

        $data = [];

        $year_all = 0;

        for ($i = 1; $i <= 12; $i++) {
            $temp = 0;
            foreach ($records as $record) {
                $month = date('m', strtotime($record->start));
                if ($i == $month) {
                    $temp++;
                }
                // 全年数据，以最后一个月来计算，这里12目的是让循环只执行一次
                if ($i == 12) {
                    $year_all++;
                }
            }
            array_push($data, $temp);
        }

        $this->withContent('全年：'.$year_all);
        // 图表数据
        $this->withChart($data);
    }

    /**
     * 设置卡片内容.
     *
     * @param string $content
     *
     * @return IssueTrend
     */
    public function withContent(string $content): IssueTrend
    {
        return $this->content(
            <<<HTML
<div class="d-flex justify-content-between align-items-center mt-1" style="margin-bottom: 2px">
    <h2 class="ml-1 font-lg-1">{$content}</h2>
</div>
HTML
        );
    }

    /**
     * 设置图表数据.
     *
     * @param array $data
     *
     * @return IssueTrend
     */
    public function withChart(array $data): IssueTrend
    {
        $this->chartOptions['tooltip']['x']['show'] = true;

        return $this->chart([
            'series' => [
                [
                    'name' => '异常次数',
                    'data' => $data,
                ],
            ],
            'tooltip' => [
                'x' => [
                    'show' => true,
                ],
            ],
            'colors' => [
                '#52338F',
            ],
        ]);
    }

    /**
     * 初始化卡片内容.
     *
     * @return void
     */
    protected function init()
    {
        parent::init();

        $this->title($this->label);
        $this->dropdown([
            'current_year' => '今年度',
            'pre_year' => '上年度',
        ]);
    }
}
