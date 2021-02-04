<?php

namespace App\Admin\Metrics;

use Closure;
use Dcat\Admin\Grid\LazyRenderable as LazyGrid;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Card;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;

class AllWorth extends Card
{
    /**
     * @param string|Closure|Renderable|LazyWidget $content
     *
     * @return AllWorth
     */
    public function content($content): AllWorth
    {
        if ($content instanceof LazyGrid) {
            $content->simple();
        }
        $device = DB::select('SELECT SUM(price) as total from device_records WHERE deleted_at IS NULL');
        $device = $device[0]->total;
        if (empty($device)) {
            $device = 0;
        }
        $part = DB::select('SELECT SUM(price) as total from part_records WHERE deleted_at IS NULL');
        $part = $part[0]->total;
        if (empty($part)) {
            $part = 0;
        }

        $software = DB::select('SELECT SUM(price) as total from software_records WHERE deleted_at IS NULL');
        $software = $software[0]->total;
        if (empty($software)) {
            $software = 0;
        }

        $total = $device + $part + $software;
        $html = <<<HTML
<div class="small-box" style="margin-bottom: 0;background: rgba(34,34,51,0.5);border-radius: .25rem;">
  <div class="inner">
    <h3 class="font-grey">{$total}</h3>
    <p class="font-grey">资产总价值</p>
  </div>
</div>
HTML;

        $this->content = $this->formatRenderable($html);
        $this->noPadding();

        return $this;
    }
}
