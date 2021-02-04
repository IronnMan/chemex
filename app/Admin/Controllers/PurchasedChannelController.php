<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\ToolAction\PurchasedChannelImportAction;
use App\Admin\Repositories\PurchasedChannel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;
use Illuminate\Http\Request;

class PurchasedChannelController extends AdminController
{
    public function selectList(Request $request)
    {
        $q = $request->get('q');

        return \App\Models\PurchasedChannel::where('name', 'like', "%$q%")
            ->paginate(null, ['id', 'name as text']);
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        return Grid::make(new PurchasedChannel(), function (Grid $grid) {
            $grid->column('id');
            $grid->column('name');
            $grid->column('description');
            $grid->column('created_at');
            $grid->column('updated_at');

            $grid->toolsWithOutline(false);

            $grid->tools([
                new PurchasedChannelImportAction(),
            ]);

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
        return Show::make($id, new PurchasedChannel(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('description');
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
        return Form::make(new PurchasedChannel(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->text('description');

            $form->display('created_at');
            $form->display('updated_at');

            $form->disableCreatingCheck();
            $form->disableEditingCheck();
            $form->disableViewCheck();
        });
    }
}
