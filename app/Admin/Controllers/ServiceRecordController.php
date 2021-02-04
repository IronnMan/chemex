<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\RowAction\ServiceIssueCreateAction;
use App\Admin\Actions\Grid\RowAction\ServiceRecordDeleteAction;
use App\Admin\Actions\Grid\RowAction\ServiceTrackCreateUpdateAction;
use App\Admin\Grid\Displayers\RowActions;
use App\Admin\Repositories\ServiceRecord;
use App\Models\DeviceRecord;
use App\Support\Data;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Tab;

/**
 * @property  DeviceRecord device
 */
class ServiceRecordController extends AdminController
{
    public function index(Content $content): Content
    {
        return $content
            ->title($this->title())
            ->description(trans('admin.list'))
            ->body(function (Row $row) {
                $tab = new Tab();
                $tab->add(Data::icon('record').trans('main.record'), $this->grid(), true);
                $tab->addLink(Data::icon('track').trans('main.track'), route('service.tracks.index'));
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
        return Grid::make(new ServiceRecord(['device']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('name');
            $grid->column('description');
            $grid->column('status')->switch('green');
            $grid->column('device.name')->link(function () {
                if (! empty($this->device)) {
                    return route('device.records.show', $this->device['id']);
                }
            });
            $grid->actions(function (RowActions $actions) {
                if (Admin::user()->can('service.record.delete')) {
                    $actions->append(new ServiceRecordDeleteAction());
                }
                if (Admin::user()->can('service.track.create_update')) {
                    $actions->append(new ServiceTrackCreateUpdateAction());
                }
                if (Admin::user()->can('service.issue.create')) {
                    $actions->append(new ServiceIssueCreateAction());
                }
            });

            $grid->enableDialogCreate();
            $grid->disableRowSelector();
            $grid->disableDeleteButton();
            $grid->disableBatchActions();

            $grid->toolsWithOutline(false);

            $grid->quickSearch('id', 'name', 'description', 'device.name')
                ->placeholder(trans('main.quick_search'))
                ->auto(false);

            $grid->export();
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id): Show
    {
        return Show::make($id, new ServiceRecord(['device']), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('description');
            $show->field('device.name');
            $show->field('created_at');
            $show->field('updated_at');

            $show->disableDeleteButton();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form(): Form
    {
        return Form::make(new ServiceRecord(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->text('description');
            $form->switch('status')
                ->default(0)
                ->help(trans('service_record_status_help'));

            $form->display('created_at');
            $form->display('updated_at');

            $form->disableDeleteButton();

            $form->disableCreatingCheck();
            $form->disableEditingCheck();
            $form->disableViewCheck();
        });
    }
}
