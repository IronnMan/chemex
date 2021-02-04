<?php

namespace App\Admin\Metrics;

use Closure;
use Dcat\Admin\Grid\LazyRenderable as LazyGrid;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Card;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;

class PartWorth extends Card
{
    /**
     * @param string|Closure|Renderable|LazyWidget $content
     *
     * @return PartWorth
     */
    public function content($content): PartWorth
    {
        if ($content instanceof LazyGrid) {
            $content->simple();
        }
        $total = DB::select('SELECT SUM(price) as total from part_records WHERE deleted_at IS NULL');
        $total = $total[0]->total;
        if (empty($total)) {
            $total = 0;
        }
        $html = <<<HTML
<div class="small-box" style="margin-bottom: 0;background: rgba(34,34,51,0.5);border-radius: .25rem">
  <div class="inner">
    <h3 class="font-grey">{$total}</h3>
    <p class="font-grey">配件总价值</p>
  </div>
</div>
HTML;

        $this->content = $this->formatRenderable($html);
        $this->noPadding();

        return $this;
    }
}
