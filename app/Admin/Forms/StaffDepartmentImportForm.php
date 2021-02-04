<?php

namespace App\Admin\Forms;

use App\Models\StaffDepartment;
use App\Services\LDAPService;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Widgets\Form;
use Dcat\EasyExcel\Excel;
use Exception;
use League\Flysystem\FileNotFoundException;

class StaffDepartmentImportForm extends Form
{
    /**
     * 处理表单提交逻辑.
     * @param array $input
     * @return JsonResponse
     */
    public function handle(array $input): JsonResponse
    {
        if ($input['type'] == 'file') {
            $file = $input['file'];
            $file_path = public_path('uploads/'.$file);
            try {
                $rows = Excel::import($file_path)->first()->toArray();
                foreach ($rows as $row) {
                    try {
                        if (! empty($row['名称'])) {
                            $staff_department = new StaffDepartment();
                            $staff_department->name = $row['名称'];
                            if (! empty($row['描述'])) {
                                $staff_department->description = $row['描述'];
                            }
                            if (! empty($row['父级部门'])) {
                                $parent_department = StaffDepartment::where('name', $row['父级部门'])->first();
                                if (empty($parent_department)) {
                                    $parent_department = new StaffDepartment();
                                    $parent_department->name = $row['父级部门'];
                                    $parent_department->save();
                                }
                                $staff_department->parent_id = $parent_department->id;
                            }
                            $staff_department->save();
                        } else {
                            return $this->response()
                                ->error('缺少必要的字段！');
                        }
                    } catch (Exception $exception) {
                        return $this->response()->error($exception->getMessage());
                    }
                }

                return $this->response()
                    ->success('文件导入成功！')
                    ->refresh();
            } catch (IOException $e) {
                return $this->response()
                    ->error('文件读写失败：'.$e->getMessage());
            } catch (UnsupportedTypeException $e) {
                return $this->response()
                    ->error('不支持的文件类型：'.$e->getMessage());
            } catch (FileNotFoundException $e) {
                return $this->response()
                    ->error('文件不存在：'.$e->getMessage());
            }
        }

        if ($input['type'] == 'ldap') {
            $result = LDAPService::importStaffDepartments($input['mode']);
            if ($result) {
                return $this->response()
                    ->success('LDAP导入成功！')
                    ->refresh();
            } else {
                return $this->response()
                    ->error($result);
            }
        }
    }

    /**
     * 构造表单.
     */
    public function form()
    {
        $this->select('type')
            ->when('file', function (Form $form) {
                $form->file('file', '表格文件')
                    ->help('导入支持xls、xlsx、csv文件，且表格头必填栏位【名称】，可选栏位【描述、父级部门】。')
                    ->accept('xls,xlsx,csv')
                    ->autoUpload();
            })
            ->when('ldap', function (Form $form) {
                $form->radio('mode')
                    ->options(['rewrite' => '覆盖', 'merge' => '合并'])
                    ->required()
                    ->default('merge');
            })
            ->options(['file' => '文件', 'ldap' => '域控'])
            ->required()
            ->default('file');
    }
}
