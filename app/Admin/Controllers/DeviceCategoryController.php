<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Tree\ToolAction\DeviceCategoryImportAction;
use App\Admin\Repositories\DeviceCategory;
use App\Models\DepreciationRule;
use App\Support\Data;
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

class DeviceCategoryController extends AdminController
{
    public function index(Content $content): Content
    {
        return $content
            ->title($this->title())
            ->description(trans('admin.list'))
            ->body(function (Row $row) {
                $tab = new Tab();
                $tab->addLink(Data::icon('record').trans('main.record'), route('device.records.index'));
                $tab->add(Data::icon('category').trans('main.category'), $this->treeView(), true);
                $tab->addLink(Data::icon('track').trans('main.track'), route('device.tracks.index'));
                $row->column(12, $tab);
            });
    }

    protected function treeView(): Tree
    {
        return new Tree(new \App\Models\DeviceCategory(), function (Tree $tree) {
            $tree->tools(function (Tree\Tools $tools) {
                $tools->add(new DeviceCategoryImportAction());
            });
        });
    }

    public function selectList(Request $request)
    {
        $q = $request->get('q');

        return \App\Models\DeviceCategory::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        return Grid::make(new DeviceCategory(['parent', 'depreciation']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('name');
            $grid->column('description');
            $grid->column('parent.name');
            $grid->column('depreciation.name');

            $grid->toolsWithOutline(false);
            $grid->enableDialogCreate();

            $grid->quickSearch('id', 'name', 'description')
                ->placeholder(trans('main.quick_search'))
                ->auto(false);
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
        return Show::make($id, new DeviceCategory(['parent', 'depreciation']), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('description');
            $show->field('parent.name');
            $show->field('depreciation.name');
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
        return Form::make(new DeviceCategory(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->text('description');

            if (Support::ifSelectCreate()) {
                $form->selectCreate('parent_id', admin_trans_label('Parent'))
                    ->options(\App\Models\DeviceCategory::class)
                    ->ajax(route('selection.device.categories'))
                    ->url(route('device.categories.create'));
            } else {
                $form->select('parent_id', admin_trans_label('Parent'))
                    ->options(\App\Models\DeviceCategory::pluck('name', 'id'));
            }

            if (Support::ifSelectCreate()) {
                $form->selectCreate('depreciation_rule_id', admin_trans_label('Depreciation Rule Id'))
                    ->options(DepreciationRule::class)
                    ->ajax(route('selection.depreciation.rules'))
                    ->url(route('depreciation.rules.create'));
            } else {
                $form->select('depreciation_rule_id', admin_trans_label('Depreciation Rule Id'))
                    ->options(DepreciationRule::pluck('name', 'id'));
            }

            $form->display('created_at');
            $form->display('updated_at');

            $form->disableCreatingCheck();
            $form->disableEditingCheck();
            $form->disableViewCheck();
        });
    }
}
