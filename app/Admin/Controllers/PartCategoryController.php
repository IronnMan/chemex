<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Tree\ToolAction\PartCategoryImportAction;
use App\Admin\Repositories\PartCategory;
use App\Models\DepreciationRule;
use App\Support\Data;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Show;
use Dcat\Admin\Tree;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Http\Request;

class PartCategoryController extends AdminController
{
    public function selectList(Request $request)
    {
        $q = $request->get('q');

        return \App\Models\PartCategory::where('name', 'like', "%$q%")
            ->paginate(null, ['id', 'name as text']);
    }

    public function index(Content $content): Content
    {
        return $content
            ->title($this->title())
            ->description(trans('admin.list'))
            ->body(function (Row $row) {
                $tab = new Tab();
                $tab->addLink(Data::icon('record').trans('main.record'), route('part.records.index'));
                $tab->add(Data::icon('category').trans('main.category'), $this->treeView(), true);
                $tab->addLink(Data::icon('track').trans('main.track'), route('part.tracks.index'));
                $row->column(12, $tab);
            });
    }

    protected function treeView(): Tree
    {
        return new Tree(new \App\Models\PartCategory(), function (Tree $tree) {
            $tree->tools(function (Tree\Tools $tools) {
                $tools->add(new PartCategoryImportAction());
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
        return Grid::make(new PartCategory(['parent', 'depreciation']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('name');
            $grid->column('description');
            $grid->column('parent.name');
            $grid->column('depreciation.name');

            $grid->enableDialogCreate();

            $grid->toolsWithOutline(false);

            $grid->quickSearch('id', 'name', 'description')
                ->placeholder(trans('admin.quick_search'))
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
        return Show::make($id, new PartCategory(['parent', 'depreciation']), function (Show $show) {
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
        return Form::make(new PartCategory(['parent', 'depreciation']), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->text('description');
            $form->select('parent_id')
                ->options(\App\Models\PartCategory::pluck('name', 'id'));
            $form->select('depreciation_rule_id')
                ->options(DepreciationRule::pluck('name', 'id'));
            $form->display('created_at');
            $form->display('updated_at');

            $form->disableCreatingCheck();
            $form->disableEditingCheck();
            $form->disableViewCheck();
        });
    }
}
