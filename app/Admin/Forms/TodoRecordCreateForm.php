<?php

namespace App\Admin\Forms;

use App\Models\AdminUser;
use App\Models\TodoRecord;
use App\Support\Data;
use Dcat\Admin\Admin;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Widgets\Form;
use Exception;

class TodoRecordCreateForm extends Form
{
    /**
     * 处理表单提交逻辑.
     * @param array $input
     * @return JsonResponse
     */
    public function handle(array $input): JsonResponse
    {
        if (! Admin::user()->can('todo.record.create')) {
            return $this->response()
                ->error('你没有权限执行此操作！')
                ->refresh();
        }

        $name = $input['name'] ?? null;
        $start = $input['start'] ?? null;

        $priority = $input['priority'] ?? null;
        $description = $input['description'] ?? null;
        $user_id = $input['user_id'] ?? null;
        $tags = $input['tags'] ?? null;
        if (empty($name) || empty($start)) {
            return $this->response()
                ->error('缺少必要的字段！');
        }
        try {
            $todo_record = new TodoRecord();
            $todo_record->name = $name;
            $todo_record->start = $start;
            $todo_record->priority = $priority;
            $todo_record->description = $description;
            $todo_record->user_id = $user_id;
            $todo_record->tags = $tags;
            $todo_record->save();
            $return = $this
                ->response()
                ->success('成功！')
                ->refresh();
        } catch (Exception $e) {
            $return = $this
                ->response()
                ->error('失败：'.$e->getMessage());
        }

        return $return;
    }

    /**
     * 构造表单.
     */
    public function form()
    {
        $this->text('name')->required();
        $this->datetime('start')->required();
        $this->divider();
        $this->select('priority')
            ->options(Data::priority())
            ->default('normal');
        $this->textarea('description');

        $this->select('user_id', admin_trans_label('User Id'))
            ->options(AdminUser::all()
                ->pluck('name', 'id'));
        $this->tags('tags')
            ->help('随意打上标签，输入后按空格新增。');
    }
}
