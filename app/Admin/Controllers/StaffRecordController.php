<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\BatchAction\StaffRecordBatchDeleteAction;
use App\Admin\Actions\Grid\RowAction\StaffRecordDeleteAction;
use App\Admin\Actions\Grid\ToolAction\StaffRecordImportAction;
use App\Admin\Grid\Displayers\RowActions;
use App\Admin\Repositories\StaffRecord;
use App\Models\StaffDepartment;
use App\Support\Data;
use App\Support\Support;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Http\Request;

/**
 * @property int ad_tag
 */
class StaffRecordController extends AdminController
{
    public function index(Content $content): Content
    {
        return $content
            ->title($this->title())
            ->description(trans('admin.list'))
            ->body(function (Row $row) {
                $tab = new Tab();
                $tab->add('雇员', $this->grid(), true);
                $tab->addLink('部门', route('staff.departments.index'));
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
        return Grid::make(new StaffRecord(['department']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('name')->display(function ($name) {
                if ($this->ad_tag === 1) {
                    return "<span class='badge badge-primary mr-1'>AD</span>$name";
                }

                return $name;
            });
            $grid->column('department.name');
            $grid->column('gender');
            $grid->column('title');
            $grid->column('mobile');
            $grid->column('email');

            $grid->enableDialogCreate();
            $grid->disableDeleteButton();
            $grid->disableBatchDelete();

            $grid->batchActions([
                new StaffRecordBatchDeleteAction(),
            ]);

            $grid->actions(function (RowActions $actions) {
                if (Admin::user()->can('staff.record.delete')) {
                    $actions->append(new StaffRecordDeleteAction());
                }
            });

            $grid->toolsWithOutline(false);

            $grid->tools([
                new StaffRecordImportAction(),
            ]);

            $grid->quickSearch('id', 'name', 'department.name', 'gender', 'title', 'mobile', 'email')
                ->placeholder('试着搜索一下')
                ->auto(false);

            $grid->export();
        });
    }

    public function selectList(Request $request)
    {
        $q = $request->get('q');

        return \App\Models\StaffRecord::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
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
        return Show::make($id, new StaffRecord(['department']), function (Show $show) {
            $show->field('id');
            $show->field('name')->unescape()->as(function ($name) {
                if ($this->ad_tag === 1) {
                    return "<span class='badge badge-primary mr-1'>AD</span>$name";
                }

                return $name;
            });
            $show->field('department.name');
            $show->field('gender');
            $show->field('title');
            $show->field('mobile');
            $show->field('email');
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
        return Form::make(new StaffRecord(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            if (Support::ifSelectCreate()) {
                $form->selectCreate('department_id', admin_trans_label('Department'))
                    ->options(StaffDepartment::class)
                    ->ajax(route('selection.staff.departments'))
                    ->url(route('staff.departments.create'))
                    ->default(0);
            } else {
                $form->select('department_id', admin_trans_label('Department'))
                    ->options(StaffDepartment::selectOptions())
                    ->required();
            }
            $form->select('gender')
                ->options(Data::genders())
                ->required();
            $form->text('title');
            $form->mobile('mobile');
            $form->email('email');

            $form->display('created_at');
            $form->display('updated_at');

            $form->disableDeleteButton();

            $form->disableCreatingCheck();
            $form->disableEditingCheck();
            $form->disableViewCheck();
        });
    }
}
