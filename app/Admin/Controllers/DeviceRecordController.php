<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\BatchAction\DeviceRecordBatchDeleteAction;
use App\Admin\Actions\Grid\RowAction\DeviceRecordDeleteAction;
use App\Admin\Actions\Grid\RowAction\DeviceTrackCreateUpdateAction;
use App\Admin\Actions\Grid\RowAction\MaintenanceCreateAction;
use App\Admin\Actions\Grid\ToolAction\DeviceRecordImportAction;
use App\Admin\Grid\Displayers\RowActions;
use App\Admin\Repositories\DeviceRecord;
use App\Models\DepreciationRule;
use App\Models\DeviceCategory;
use App\Models\PurchasedChannel;
use App\Models\StaffDepartment;
use App\Models\VendorRecord;
use App\Services\DeviceService;
use App\Services\ExpirationService;
use App\Services\ExportService;
use App\Support\Data;
use App\Support\Support;
use App\Traits\HasDeviceRelatedGrid;
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
use Illuminate\Http\Request;

/**
 * @property int id
 * @property float price
 * @property string purchased
 * @property int depreciation_rule_id
 */
class DeviceRecordController extends AdminController
{
    use HasDeviceRelatedGrid;

    /**
     * 详情页构建器
     * 为了复写详情页的布局
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content): Content
    {
        $name = Support::deviceIdToStaffName($id);
        $history = DeviceService::history($id);

        return $content
            ->title($this->title())
            ->description($this->description()['index'] ?? trans('admin.show'))
            ->body(function (Row $row) use ($id, $name, $history) {
                $column_b_width = 0;
                $column_c_width = 0;
                // 如果B和C权限都有
                if (Admin::user()->can('device.related') && Admin::user()->can('device.history')) {
                    $column_a_width = 4;
                    $column_b_width = 4;
                    $column_c_width = 4;
                } elseif (Admin::user()->can('device.related') && ! Admin::user()->can('device.history')) {
                    // 如果只有B
                    $column_a_width = 6;
                    $column_b_width = 6;
                } elseif (! Admin::user()->can('device.related') && Admin::user()->can('device.history')) {
                    // 如果只有C
                    $column_a_width = 6;
                    $column_c_width = 6;
                } else {
                    $column_a_width = 12;
                }
                $row->column($column_a_width, $this->detail($id));
                $row->column($column_b_width, function (Column $column) use ($id, $name) {
                    $column->row(Card::make()->content(trans('main.device_record_current_staff').'：'.$name));
                    if (Admin::user()->can('device.related')) {
                        $result = self::hasDeviceRelated($id);
                        if ($result['part']) {
                            $column->row(new Card(admin_trans('menu.part'), $result['part']));
                        }
                        if ($result['software']) {
                            $column->row(new Card(admin_trans('menu.software'), $result['software']));
                        }
                        if ($result['service']) {
                            $column->row(new Card(admin_trans('menu.service'), $result['service']));
                        }
                    }
                });
                if (Admin::user()->can('device.history')) {
                    $card = new Card(trans('main.history'), view('history')->with('data', $history));
                    $row->column($column_c_width, $card->tool('<a class="btn btn-primary btn-xs" href="'.route('export.device.history', $id).'" target="_blank">导出到 Excel</a>'));
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
        return Show::make($id, new DeviceRecord(['category', 'vendor', 'channel', 'staff', 'staff.department', 'depreciation']), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('asset_number');
            $show->field('description');
            $show->field('category.name');
            $show->field('vendor.name');
            $show->field('channel.name');
            $show->field('sn');
            $show->field('mac');
            $show->field('ip');
            $show->field('photo')->image();
            $show->field('price');
            $show->field('expiration_left_days', admin_trans_label('Depreciation Price'))->as(function () {
                $device_record = \App\Models\DeviceRecord::where('id', $this->id)->first();
                if (! empty($device_record)) {
                    $depreciation_rule_id = Support::getDepreciationRuleId($device_record);

                    return Support::depreciationPrice($this->price, $this->purchased, $depreciation_rule_id);
                }
            });
            $show->field('purchased');
            $show->field('expired');
            $show->field('staff.name');
            $show->field('staff.department.name');
            $show->field('security_password');
            $show->field('admin_password');
            $show->field('depreciation.name');
            $show->field('depreciation.termination');
            $show->field('location');
            $show->field('created_at');
            $show->field('updated_at');

            $show->disableDeleteButton();
        });
    }

    public function selectList(Request $request)
    {
        $q = $request->get('q');

        return \App\Models\DeviceRecord::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }

    public function index(Content $content): Content
    {
        return $content
            ->header($this->title())
            ->description($this->description()['index'] ?? trans('admin.list'))
            ->body(function (Row $row) {
                $tab = new Tab();
                $tab->add(Data::icon('record').trans('main.record'), $this->grid(), true);
                $tab->addLink(Data::icon('category').trans('main.category'), route('device.categories.index'));
                $tab->addLink(Data::icon('track').trans('main.track'), route('device.tracks.index'));
                $row->column(12, $tab);

//                $row->column(12, function (Column $column) {
//                    $column->row(function (Row $row) {
//                        $row->column(3, new CheckDevicePercentage());
//                        $row->column(3, new DeviceAboutToExpireCounts());
//                        $row->column(3, new DeviceExpiredCounts());
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
        return Grid::make(new DeviceRecord(['category', 'vendor', 'staff', 'staff.department', 'depreciation']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('qrcode')->qrcode(function () {
                return 'device:'.$this->id;
            }, 200, 200);
            $grid->column('asset_number');
            $grid->column('photo')->image('', 50, 50);
            $grid->column('name')->display(function ($name) {
                $tag = Support::getSoftwareIcon($this->id);
                if (empty($tag)) {
                    return $name;
                } else {
                    return "<img alt='$tag' src='/static/images/icons/$tag.png' style='width: 25px;height: 25px;margin-right: 10px'/>$name";
                }
            });
            $grid->column('description');
            $grid->column('category.name');
            $grid->column('vendor.name');
            $grid->column('sn');
            $grid->column('mac');
            $grid->column('ip');
            $grid->column('price');
            $grid->column('expired');
            $grid->column('staff.name');
            $grid->column('staff.department.name');
            $grid->column('expiration_left_days', admin_trans_label('Expiration Left Days'))->display(function () {
                return ExpirationService::itemExpirationLeftDaysRender('device', $this->id);
            });
            $grid->column('depreciation.name');
            $grid->column('location');

            $grid->disableBatchDelete();
            $grid->disableDeleteButton();

            $grid->batchActions([
                new DeviceRecordBatchDeleteAction(),
            ]);

            $grid->tools([
                new DeviceRecordImportAction(),
            ]);

            $grid->actions(function (RowActions $actions) {
                if (Admin::user()->can('device.record.delete')) {
                    $actions->append(new DeviceRecordDeleteAction());
                }
                if (Admin::user()->can('device.track.create_update')) {
                    $actions->append(new DeviceTrackCreateUpdateAction());
                }
                if (Admin::user()->can('device.maintenance.create')) {
                    $actions->append(new MaintenanceCreateAction('device'));
                }
            });

            $grid->showColumnSelector();
            $grid->hideColumns(['description', 'price', 'expired', 'depreciation.name', 'location']);

            $grid->quickSearch(
                'id',
                'name',
                'asset_number',
                'description',
                'category.name',
                'vendor.name',
                'sn',
                'mac',
                'ip',
                'price',
                'staff.name',
                'staff.department.name',
                'location'
            )
                ->placeholder(trans('main.quick_search'))
                ->auto(false);

            $grid->filter(function ($filter) {
                $filter->equal('category_id')->select(DeviceCategory::pluck('name', 'id'));
                $filter->equal('vendor_id')->select(VendorRecord::pluck('name', 'id'));
                $filter->equal('staff.department_id')->select(StaffDepartment::pluck('name', 'id'));
                $filter->equal('depreciation_id')->select(DepreciationRule::pluck('name', 'id'));
                $filter->equal('location');
            });

            $grid->toolsWithOutline(false);
            $grid->export();
        });
    }

    /**
     * 履历导出.
     * @param $device_id
     * @return mixed
     */
    public function exportHistory($device_id)
    {
        return ExportService::DeviceHistory($device_id);
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form(): Form
    {
        return Form::make(new DeviceRecord(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();

            if (Support::ifSelectCreate()) {
                $form->selectCreate('category_id', admin_trans_label('Category'))
                    ->options(DeviceCategory::class)
                    ->ajax(route('selection.device.categories'))
                    ->url(route('device.categories.create'))
                    ->required();
                $form->selectCreate('vendor_id', admin_trans_label('Vendor'))
                    ->options(VendorRecord::class)
                    ->ajax(route('selection.vendor.records'))
                    ->url(route('vendor.records.create'))
                    ->required();
            } else {
                $form->select('category_id', admin_trans_label('Category'))
                    ->options(DeviceCategory::pluck('name', 'id'))
                    ->required();
                $form->select('vendor_id', admin_trans_label('Vendor'))
                    ->options(VendorRecord::pluck('name', 'id'))
                    ->required();
            }

            $form->divider();
            $form->text('asset_number');
            $form->text('description');

            if (Support::ifSelectCreate()) {
                $form->selectCreate('purchased_channel_id', admin_trans_label('Purchased Channel Id'))
                    ->options(PurchasedChannel::class)
                    ->ajax(route('selection.purchased.channels'))
                    ->url(route('purchased.channels.create'));
            } else {
                $form->select('purchased_channel_id', admin_trans_label('Purchased Channel Id'))
                    ->options(PurchasedChannel::pluck('name', 'id'));
            }

            $form->text('sn');
            $form->text('mac');
            $form->text('ip');
            $form->image('photo')
                ->autoUpload()
                ->uniqueName()
                ->help(trans('device_record_photo_help'));
            $form->currency('price');
            $form->date('purchased');
            $form->date('expired');
            $form->password('security_password')
                ->help(trans('main.device_record_security_password_help'));
            $form->password('admin_password')
                ->help(trans('main.device_record_admin_password_help'));

            if (Support::ifSelectCreate()) {
                $form->selectCreate('depreciation_rule_id', admin_trans_label('Depreciation Rule Id'))
                    ->options(DepreciationRule::class)
                    ->ajax(route('selection.depreciation.rules'))
                    ->url(route('depreciation.rules.create'))
                    ->help(trans('main.device_record_depreciation_rule_id_help'));
            } else {
                $form->select('depreciation_rule_id', admin_trans_label('Depreciation Rule Id'))
                    ->options(DepreciationRule::pluck('name', 'id'))
                    ->help(trans('main.device_record_depreciation_rule_id_help'));
            }

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
