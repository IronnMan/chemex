<?php

namespace App\Admin\Forms;

use App\Models\ServiceIssue;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class ServiceIssueUpdateForm extends Form implements LazyRenderable
{
    use LazyWidget;

    /**
     * 处理表单提交逻辑.
     * @param array $input
     * @return JsonResponse
     */
    public function handle(array $input): JsonResponse
    {
        if (! Admin::user()->can('service.issue.update')) {
            return $this->response()
                ->error('你没有权限执行此操作！')
                ->refresh();
        }

        // 获取异常id
        $issue_id = $this->payload['id'] ?? null;

        // 获取修复说明
        $description = $input['description'] ?? null;

        // 如果没有盘点id返回错误
        if (! $issue_id) {
            return $this->response()
                ->error('参数错误');
        }

        $service_issue = ServiceIssue::where('id', $issue_id)->first();
        if (empty($service_issue)) {
            return $this->response()
                ->error('没有找到此服务程序异常');
        } else {
            $service_issue->status = 2;
            $service_issue->description = $description;
            $service_issue->checker = Admin::user()->id;
            $service_issue->save();
        }

        return $this->response()
            ->success('服务程序异常处理成功')
            ->refresh();
    }

    /**
     * 构造表单.
     */
    public function form()
    {
        $this->textarea('description', '描述');
    }
}
