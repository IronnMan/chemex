<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\RowAction\TodoRecordUpdateAction;
use App\Admin\Actions\Grid\ToolAction\TodoRecordCreateAction;
use App\Admin\Grid\Displayers\RowActions;
use App\Admin\Repositories\TodoRecord;
use App\Models\DeviceRecord;
use App\Support\Data;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Alert;

/**
 * @property  DeviceRecord device
 * @property  int id
 * @property  string deleted_at
 */
class TodoRecordController extends AdminController
{
    public function index(Content $content): Content
    {
        return $content
            ->title($this->title())
            ->description($this->description()['index'] ?? trans('admin.list'))
            ->body(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->row(function (Row $row) {
                    });
                });
                $row->column(12, $this->grid());
            });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        return Grid::make(new TodoRecord(['user']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('name');
            $grid->column('start');
            $grid->column('end');
            $grid->column('priority')->using(Data::priority());
            $grid->column('user.name');
            $grid->column('tags')->explode()->label();
            $grid->column('emoji');

            $grid->actions(function (RowActions $actions) {
                if (empty($this->end)) {
                    $actions->append(new TodoRecordUpdateAction());
                }
            });

            $grid->tools([
                new TodoRecordCreateAction(),
            ]);

            $grid->disableCreateButton();
            $grid->disableEditButton();

            $grid->toolsWithOutline(false);

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
        return Show::make($id, new TodoRecord(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('description');
            $show->field('start');
            $show->field('end');
            $show->field('priority');
            $show->field('user_id');
            $show->field('tags');
            $show->field('done_description');
            $show->field('emoji');
            $show->field('created_at');
            $show->field('updated_at');

            $show->disableDeleteButton();
        });
    }

    /**
     * Make a form builder.
     * @return Alert
     */
    protected function form(): Alert
    {
        return Data::unsupportedOperationWarning();
    }
}
