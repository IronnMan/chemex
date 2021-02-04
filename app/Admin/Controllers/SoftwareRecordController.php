<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\BatchAction\SoftwareRecordBatchDeleteAction;
use App\Admin\Actions\Grid\RowAction\SoftwareRecordDeleteAction;
use App\Admin\Actions\Grid\RowAction\SoftwareTrackCreateUpdateAction;
use App\Admin\Actions\Grid\RowAction\SoftwareTrackDeleteAction;
use App\Admin\Actions\Grid\ToolAction\SoftwareRecordImportAction;
use App\Admin\Grid\Displayers\RowActions;
use App\Admin\Repositories\SoftwareRecord;
use App\Admin\Repositories\SoftwareTrack;
use App\Models\DeviceRecord;
use App\Models\PurchasedChannel;
use App\Models\SoftwareCategory;
use App\Models\VendorRecord;
use App\Services\ExpirationService;
use App\Services\SoftwareService;
use App\Support\Data;
use App\Support\Support;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Tab;

/**
 * @property  DeviceRecord device
 * @property  int id
 * @property  string deleted_at
 */
class SoftwareRecordController extends AdminController
{
    public function index(Content $content): Content
    {
        return $content
            ->title($this->title())
            ->description($this->description()['index'] ?? trans('admin.list'))
            ->body(function (Row $row) {
                $tab = new Tab();
                $tab->add(Data::icon('record').trans('main.record'), $this->grid(), true);
                $tab->addLink(Data::icon('category').trans('main.category'), route('software.categories.index'));
                $tab->addLink(Data::icon('track').trans('main.track'), route('software.tracks.index'));
                $row->column(12, $tab);

//                $row->column(12, function (Column $column) {
//                    $column->row(function (Row $row) {
//                        $row->column(3, new CheckSoftwarePercentage());
//                        $row->column(3, new SoftwareAboutToExpireCounts());
//                        $row->column(3, new SoftwareExpiredCounts());
//                    });
//                });
//                $row->column(12, $this->grid());
            });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        return Grid::make(new SoftwareRecord(['category', 'vendor']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('qrcode')->qrcode(function () {
                return 'software:'.$this->id;
            }, 200, 200);
            $grid->column('name');
            $grid->column('description');
            $grid->column('asset_number');
            $grid->column('category.name');
            $grid->column('version');
            $grid->column('vendor.name');
            $grid->column('price');
            $grid->column('purchased');
            $grid->column('expired');
            $grid->column('distribution')->using(Data::distribution());
            $grid->column('counts');
            $grid->column('left_counts')->display(function () {
                return Support::leftSoftwareCounts($this->id);
            });
            $grid->column('expiration_left_days')->display(function () {
                return ExpirationService::itemExpirationLeftDaysRender('software', $this->id);
            });
            $grid->column('location');

            $grid->actions(function (RowActions $actions) {
                if (Admin::user()->can('software.record.delete')) {
                    $actions->append(new SoftwareRecordDeleteAction());
                }
                if (Admin::user()->can('software.track.create_update')) {
                    $actions->append(new SoftwareTrackCreateUpdateAction());
                }
                if (Admin::user()->can('software.track.list')) {
                    $tracks_route = route('software.tracks.index', ['_search_' => $this->id]);
                    $actions->append("<a href='$tracks_route'>💿 ".trans('software_record_track_action').'</a>');
                }
            });

            $grid->showColumnSelector();
            $grid->hideColumns(['description', 'price', 'expired', 'location']);

            $grid->quickSearch(
                'id',
                'name',
                'asset_number',
                'category.name',
                'version',
                'price',
                'location'
            )
                ->placeholder(trans('main.quick_search'))
                ->auto(false);

            $grid->enableDialogCreate();
            $grid->disableDeleteButton();
            $grid->disableBatchDelete();

            $grid->batchActions([
                new SoftwareRecordBatchDeleteAction(),
            ]);

            $grid->tools([
                new SoftwareRecordImportAction(),
            ]);

            $grid->toolsWithOutline(false);

            $grid->export();
        });
    }

    public function show($id, Content $content): Content
    {
        $history = SoftwareService::history($id);

        return $content
            ->title($this->title())
            ->description($this->description()['index'] ?? trans('admin.show'))
            ->body(function (Row $row) use ($id, $history) {
                // 判断权限
                if (! Admin::user()->can('software.track.list')) {
                    $row->column(12, $this->detail($id));
                } else {
                    $row->column(6, $this->detail($id));
                    $row->column(6, function (Column $column) use ($id, $history) {
                        $grid = Grid::make(new SoftwareTrack(['software', 'device', 'device.staff']), function (Grid $grid) use ($id) {
                            $grid->model()->where('software_id', '=', $id);
                            $grid->tableCollapse(false);
                            $grid->withBorder();

                            $grid->column('id');
                            $grid->column('device.name')->link(function () {
                                if (! empty($this->device)) {
                                    return route('device.records.show', $this->device['id']);
                                }
                            });
                            $grid->column('device.staff.name');

                            $grid->disableToolbar();
                            $grid->disableBatchDelete();
                            $grid->disableRowSelector();
                            $grid->disableViewButton();
                            $grid->disableEditButton();
                            $grid->disableDeleteButton();

                            $grid->actions(function (RowActions $actions) {
                                if (Admin::user()->can('software.track.disable') && $this->deleted_at == null) {
                                    $actions->append(new SoftwareTrackDeleteAction());
                                }
                            });
                        });
                        $column->row(new Card(trans('main.software_record_track_title'), $grid));
                        $card = new Card(trans('main.software_record_history_title'), view('history')->with('data', $history));
                        $column->row($card->tool('<a class="btn btn-primary btn-xs" href="'.route('export.software.history', $id).'" target="_blank">'.trans('main.export_to_excel').'</a>'));
                    });
                }
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
        return Show::make($id, new SoftwareRecord(['category', 'vendor', 'channel']), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('asset_number');
            $show->field('description');
            $show->field('category.name');
            $show->field('version');
            $show->field('vendor.name');
            $show->field('channel.name');
            $show->field('price');
            $show->field('purchased');
            $show->field('expired');
            $show->field('distribution')->using(Data::distribution());
            $show->field('counts');
            $show->field('location');
            $show->field('created_at');
            $show->field('updated_at');

            $show->disableDeleteButton();
        });
    }

    /**
     * 履历导出.
     * @param $software_id
     * @return mixed
     */
    public function exportHistory($software_id)
    {
        return SoftwareService::exportHistory($software_id);
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form(): Form
    {
        return Form::make(new SoftwareRecord(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->text('version')->required();

            if (Support::ifSelectCreate()) {
                $form->selectCreate('category_id')
                    ->options(SoftwareCategory::class)
                    ->ajax(route('selection.software.categories'))
                    ->url(route('software.categories.create'))
                    ->required();
            } else {
                $form->select('category_id')
                    ->options(SoftwareCategory::selectOptions())
                    ->required();
            }

            if (Support::ifSelectCreate()) {
                $form->selectCreate('vendor_id')
                    ->options(VendorRecord::class)
                    ->ajax(route('selection.vendor.records'))
                    ->url(route('vendor.records.create'))
                    ->required();
            } else {
                $form->select('vendor_id')
                    ->options(VendorRecord::pluck('name', 'id'))
                    ->required();
            }

            $form->select('distribution')
                ->options(Data::distribution())
                ->default('u')
                ->required();
            $form->number('counts')
                ->min(-1)
                ->default(1)
                ->required()
                ->help(trans('main.software_record_counts_help'));
            $form->divider();
            $form->text('sn');
            $form->text('description');
            $form->text('asset_number');

            if (Support::ifSelectCreate()) {
                $form->selectCreate('purchased_channel_id')
                    ->options(VendorRecord::class)
                    ->ajax(route('selection.purchased.channels'))
                    ->url(route('purchased.channels.create'));
            } else {
                $form->select('purchased_channel_id')
                    ->options(PurchasedChannel::pluck('name', 'id'));
            }

            $form->currency('price')->default(0);
            $form->date('purchased');
            $form->date('expired');
            $form->text('location')
                ->help(trans('main.location_help'));

            $form->display('created_at');
            $form->display('updated_at');

            $form->disableDeleteButton();

            $form->disableCreatingCheck();
            $form->disableEditingCheck();
            $form->disableViewCheck();
        });
    }
}
