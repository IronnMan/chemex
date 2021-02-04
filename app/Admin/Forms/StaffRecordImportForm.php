<?php

namespace App\Admin\Forms;

use App\Models\StaffDepartment;
use App\Models\StaffRecord;
use App\Services\LDAPService;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Widgets\Form;
use Dcat\EasyExcel\Excel;
use Exception;
use League\Flysystem\FileNotFoundException;

class StaffRecordImportForm extends Form
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
                        if (! empty($row['名称']) && ! empty($row['部门']) && ! empty($row['性别'])) {
                            $staff_department = StaffDepartment::where('name', $row['部门'])->first();
                            if (empty($staff_department)) {
                                $staff_department = new StaffDepartment();
                                $staff_department->name = $row['部门'];
                                $staff_department->save();
                            }
                            $staff_record = new StaffRecord();
                            $staff_record->name = $row['名称'];
                            $staff_record->department_id = $staff_department->id;
                            $staff_record->gender = $row['性别'];
                            if (! empty($row['职位'])) {
                                $staff_record->title = $row['职位'];
                            }
                            if (! empty($row['手机'])) {
                                $staff_record->mobile = $row['手机'];
                            }
                            if (! empty($row['邮箱'])) {
                                $staff_record->email = $row['邮箱'];
                            }
                            $staff_record->save();
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
            $result = LDAPService::importStaffRecords($input['mode']);
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
                    ->help('导入支持xls、xlsx、csv文件，且表格头必填栏位【名称、部门、性别】，可选栏位【职位、手机、邮箱】。')
                    ->accept('xls,xlsx,csv')
                    ->uniqueName()
                    ->autoUpload();
            })
            //TODO 这里怎么回事，上面$this，下面$form
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
