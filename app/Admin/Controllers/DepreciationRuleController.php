<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\DepreciationRule;
use Dcat\Admin\Form;
use Dcat\Admin\Form\NestedForm;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;
use Illuminate\Http\Request;

class DepreciationRuleController extends AdminController
{
    public function selectList(Request $request)
    {
        $q = $request->get('q');

        return \App\Models\DepreciationRule::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        return Grid::make(new DepreciationRule(), function (Grid $grid) {
            $grid->column('id');
            $grid->column('name');
            $grid->column('description');
            $grid->column('termination');

            $grid->toolsWithOutline(false);

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
        return Show::make($id, new DepreciationRule(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('description');
            $show->field('rules')->view('depreciation_rules.rules');
            $show->field('termination');
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
        return Form::make(new DepreciationRule(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->text('description')
                ->help(trans('main.depreciation_rule_description_help'));
            $form->table('rules', function (NestedForm $table) {
                $table->number('number')
                    ->min(0)
                    ->required();
                $table->select('scale')
                    ->options([
                        'day' => '天',
                        'month' => '月',
                        'year' => '年',
                    ])
                    ->required();
                $table->currency('ratio')
                    ->symbol(trans('main.depreciation_rule_rules_symbol'))
                    ->required();
            });
            $form->date('termination')
                ->help(trans('main.depreciation_rule_termination_help'));

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
