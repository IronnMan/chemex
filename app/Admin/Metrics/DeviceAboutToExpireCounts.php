<?php

namespace App\Admin\Metrics;

use App\Models\DeviceRecord;
use Closure;
use Dcat\Admin\Grid\LazyRenderable as LazyGrid;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Card;
use Illuminate\Contracts\Support\Renderable;

class DeviceAboutToExpireCounts extends Card
{
    /**
     * @param string|Closure|Renderable|LazyWidget $content
     *
     * @return DeviceAboutToExpireCounts
     */
    public function content($content): DeviceAboutToExpireCounts
    {
        if ($content instanceof LazyGrid) {
            $content->simple();
        }
        $from = date('Y-m-d', time());
        $to = date('Y-m-d', time() + (60 * 60 * 24 * 30));
        $counts = DeviceRecord::whereBetween('expired', [$from, $to])->count();

        $html = <<<HTML
<div class="info-box" style="background:transparent;margin-bottom: 0;padding: 0;">
<span class="info-box-icon"><i class="feather icon-monitor" style="color:rgba(255,153,76,1);"></i></span>
  <div class="info-box-content">
    <span class="info-box-text mt-1">即将过保的设备数量</span>
    <span class="info-box-number">{$counts}</span>
  </div>
</div>
HTML;

        $this->content = $this->formatRenderable($html);
        $this->noPadding();

        return $this;
    }
}
