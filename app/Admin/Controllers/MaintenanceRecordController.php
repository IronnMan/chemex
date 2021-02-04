<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\RowAction\MaintenanceUpdateAction;
use App\Admin\Grid\Displayers\RowActions;
use App\Admin\Repositories\MaintenanceRecord;
use App\Support\Data;
use App\Support\Support;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools\Selector;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Alert;

/**
 * @property int item_id
 * @property string item
 * @property int status
 */
class MaintenanceRecordController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        return Grid::make(new MaintenanceRecord(), function (Grid $grid) {
            $grid->model()->orderBy('status', 'ASC');

            $grid->column('id');
            $grid->column('item')->using(Data::items());
            $grid->column('item_id')->display(function () {
                return Support::itemIdToItemName($this->item, $this->item_id);
            });
            $grid->column('ng_description')->limit(30);
            $grid->column('ok_description')->limit(30);
            $grid->column('ng_time');
            $grid->column('ok_time');
            $grid->column('status')->using(Data::maintenanceStatus());

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->disableBatchActions();
            $grid->disableRowSelector();

            $grid->toolsWithOutline(false);

            $grid->actions(function (RowActions $actions) {
                if ($this->status == 0 && Admin::user()->can('maintenance.update')) {
                    $actions->append(new MaintenanceUpdateAction());
                }
            });

            $grid->quickSearch('id', 'item', 'ng_description', 'ok_description')
                ->placeholder(trans('main.quick_search'))
                ->auto(false);

            $grid->selector(function (Selector $selector) {
                $selector->select('status', [
                    0 => trans('main.maintenance_record_status_waiting'),
                    1 => trans('main.maintenance_record_status_done'),
                    2 => trans('main.maintenance_record_status_cancelled'),
                ]);
            });

            $grid->export();
        });
    }

    /**
     * Make a show builder.
     *
     * @return Alert
     */
    protected function detail(): Alert
    {
        return Data::unsupportedOperationWarning();
    }

    /**
     * Make a form builder.
     *
     * @return Alert
     */
    protected function form(): Alert
    {
        return Data::unsupportedOperationWarning();
    }
}
