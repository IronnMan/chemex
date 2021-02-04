<?php

namespace App\Admin\Metrics;

use App\Models\StaffRecord;
use Closure;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Card;
use Illuminate\Contracts\Support\Renderable;

class StaffCounts extends Card
{
    /**
     * @param string|Closure|Renderable|LazyWidget $content
     *
     * @return $this
     */
    public function content($content): StaffCounts
    {
        $counts = StaffRecord::count();
        $html = <<<HTML
<div class="small-box" style="margin-bottom: 0;background: rgba(139,195,74,0.7);border-radius: .25rem">
  <div class="inner">
    <h3 class="font-grey">{$counts}</h3>
    <p class="font-grey">雇员数量</p>
  </div>
</div>
HTML;

        $this->content = $this->formatRenderable($html);
        $this->noPadding();

        return $this;
    }
}
