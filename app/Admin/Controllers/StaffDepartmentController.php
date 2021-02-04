<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Tree\ToolAction\StaffDepartmentImportAction;
use App\Admin\Repositories\StaffDepartment;
use App\Support\Support;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Show;
use Dcat\Admin\Tree;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Http\Request;

class StaffDepartmentController extends AdminController
{
    public function selectList(Request $request)
    {
        $q = $request->get('q');

        return \App\Models\StaffDepartment::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }

    public function index(Content $content): Content
    {
        return $content
            ->title($this->title())
            ->description(trans('admin.list'))
            ->body(function (Row $row) {
                $tab = new Tab();
                $tab->addLink('雇员', route('staff.records.index'));
                $tab->add('部门', $this->treeView(), true);
                $row->column(12, $tab);
            });
    }

    protected function treeView(): Tree
    {
        return new Tree(new \App\Models\StaffDepartment(), function (Tree $tree) {
            $tree->branch(function ($branch) {
                $display = "{$branch['name']}";
                if ($branch['ad_tag'] === 1) {
                    $display = "<span class='badge badge-primary mr-1'>AD</span>".$display;
                }

                return $display;
            });
            $tree->tools(function (Tree\Tools $tools) {
                $tools->add(new StaffDepartmentImportAction());
            });
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        return Grid::make(new StaffDepartment(['parent']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('name');
            $grid->column('description');
            $grid->column('parent.name');

            $grid->enableDialogCreate();

            $grid->toolsWithOutline(false);

            $grid->quickSearch('id', 'name', 'description', 'parent.name');
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
        return Show::make($id, new StaffDepartment(['parent']), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('description');
            $show->field('parent.name');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form(): Form
    {
        return Form::make(new StaffDepartment(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->text('description');
            if (Support::ifSelectCreate()) {
                $form->selectCreate('parent_id', admin_trans_label('Parent'))
                    ->options(\App\Models\StaffDepartment::class)
                    ->ajax(route('selection.staff.departments'))
                    ->url(route('staff.departments.create'))
                    ->default(0);
            } else {
                $form->select('parent_id', admin_trans_label('Parent'))
                    ->options(\App\Models\StaffDepartment::pluck('name', 'id'))
                    ->default(0);
            }

            $form->display('created_at');
            $form->display('updated_at');

            $form->disableCreatingCheck();
            $form->disableEditingCheck();
            $form->disableViewCheck();
        });
    }
}
