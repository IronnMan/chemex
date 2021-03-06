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
     * 处理表单提交逻辑
     * @param array $input
     * @return JsonResponse
     */
    public function handle(array $input): JsonResponse
    {
        if ($input['type'] == 'file') {
            $file = $input['file'];
            $file_path = public_path('uploads/' . $file);
            try {
                $rows = Excel::import($file_path)->first()->toArray();
                foreach ($rows as $row) {
                    try {
                        if (!empty($row['名称']) && !empty($row['部门']) && !empty($row['性别'])) {
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
                            if (!empty($row['职位'])) {
                                $staff_record->title = $row['职位'];
                            }
                            if (!empty($row['手机'])) {
                                $staff_record->mobile = $row['手机'];
                            }
                            if (!empty($row['邮箱'])) {
                                $staff_record->email = $row['邮箱'];
                            }
                            $staff_record->save();
                        } else {
                            return $this->response()
                                ->error(trans('main.parameter_missing'));
                        }
                    } catch (Exception $exception) {
                        return $this->response()->error($exception->getMessage());
                    }
                }
                return $this->response()
                    ->success(trans('main.upload_success'))
                    ->refresh();
            } catch (IOException $e) {
                return $this->response()
                    ->error(trans('main.file_io_error') . $e->getMessage());
            } catch (UnsupportedTypeException $e) {
                return $this->response()
                    ->error(trans('main.file_format') . $e->getMessage());
            } catch (FileNotFoundException $e) {
                return $this->response()
                    ->error(trans('main.file_none') . $e->getMessage());
            }
        }

        if ($input['type'] == 'ldap') {
            $result = LDAPService::importStaffRecords($input['mode']);
            if ($result) {
                return $this->response()
                    ->success(admin_trans_label('LDAP Import Success'))
                    ->refresh();
            } else {
                return $this->response()
                    ->error($result);
            }
        }
    }

    /**
     * 构造表单
     */
    public function form()
    {
        $this->select('type')
            ->when('file', function (Form $form) {
                $form->file('file')
                    ->help(admin_trans_label('File Help'))
                    ->accept('xls,xlsx,csv')
                    ->uniqueName()
                    ->autoUpload();
            })
            //TODO 这里怎么回事，上面$this，下面$form
            ->when('ldap', function (Form $form) {
                $form->radio('mode')
                    ->options(['rewrite' => admin_trans_label('Rewrite'), 'merge' => admin_trans_label('Merge')])
                    ->required()
                    ->default('merge');
            })
            ->options(['file' => admin_trans_label('File'), 'ldap' => admin_trans_label('LDAP')])
            ->required()
            ->default('file');
    }
}
