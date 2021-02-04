<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\RowAction\ServiceTrackDeleteAction;
use App\Admin\Grid\Displayers\RowActions;
use App\Admin\Repositories\ServiceTrack;
use App\Support\Data;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\Tab;

/**
 * @property string deleted_at
 */
class ServiceTrackController extends AdminController
{
    public function index(Content $content): Content
    {
        return $content
            ->title($this->title())
            ->description(trans('admin.list'))
            ->body(function (Row $row) {
                $tab = new Tab();
                $tab->addLink(Data::icon('record').trans('main.record'), route('service.records.index'));
                $tab->add(Data::icon('track').trans('main.track'), $this->grid(), true);
                $tab->addLink(Data::icon('issue').trans('main.issue'), route('service.issues.index'));
                $row->column(12, $tab);
            });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        return Grid::make(new ServiceTrack(['service', 'device']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('service.name');
            $grid->column('device.name');
            $grid->column('created_at');
            $grid->column('updated_at');

            $grid->toolsWithOutline(false);

            $grid->disableCreateButton();
            $grid->disableRowSelector();
            $grid->disableBatchActions();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();

            $grid->actions(function (RowActions $actions) {
                if (Admin::user()->can('service.track.delete') && $this->deleted_at == null) {
                    $actions->append(new ServiceTrackDeleteAction());
                }
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->scope('history', trans('main.history'))->onlyTrashed();
            });

            $grid->quickSearch('id', 'service.name', 'device.name')
                ->placeholder(trans('admin.quick_search'))
                ->auto(false);
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
