<?php

namespace App\Admin\Forms;

use App\Models\PurchasedChannel;
use App\Models\SoftwareRecord;
use App\Models\VendorRecord;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Widgets\Form;
use Dcat\EasyExcel\Excel;
use Exception;
use League\Flysystem\FileNotFoundException;

class SoftwareRecordImportForm extends Form
{
    /**
     * 处理表单提交逻辑.
     * @param array $input
     * @return JsonResponse
     */
    public function handle(array $input): JsonResponse
    {
        $file = $input['file'];
        $file_path = public_path('uploads/'.$file);
        try {
            $rows = Excel::import($file_path)->first()->toArray();
            foreach ($rows as $row) {
                try {
                    if (! empty($row['名称']) && ! empty($row['分类']) && ! empty($row['厂商'] && ! empty($row['版本']))) {
                        $category = SoftwareRecord::where('name', $row['分类'])->first();
                        $vendor = VendorRecord::where('name', $row['厂商'])->first();
                        if (empty($category)) {
                            $category = new SoftwareRecord();
                            $category->name = $row['分类'];
                            $category->save();
                        }
                        if (empty($vendor)) {
                            $vendor = new VendorRecord();
                            $vendor->name = $row['厂商'];
                            $vendor->save();
                        }
                        $software_record = new SoftwareRecord();
                        $software_record->name = $row['名称'];
                        $software_record->category_id = $category->id;
                        $software_record->vendor_id = $vendor->id;
                        // 这里导入判断空值，不能使用 ?? null 或者 ?? '' 的方式，写入数据库的时候
                        // 会默认为插入''而不是null，这会导致像price这样的double也是插入''，就会报错
                        // 其实price应该插入null
                        $software_record->asset_number = $row['资产编号'];
                        if (! empty($row['版本'])) {
                            $software_record->version = $row['版本'];
                        }
                        if (! empty($row['价格'])) {
                            $software_record->price = $row['价格'];
                        }
                        if (! empty($row['购入日期'])) {
                            $software_record->purchased = $row['购入日期'];
                        }
                        if (! empty($row['过保日期'])) {
                            $software_record->expired = $row['过保日期'];
                        }
                        if (! empty($row['购入途径'])) {
                            $purchased_channel = PurchasedChannel::where('name', $row['购入途径'])->first();
                            if (empty($purchased_channel)) {
                                $purchased_channel = new PurchasedChannel();
                                $purchased_channel->name = $row['购入途径'];
                                $purchased_channel->save();
                            }
                            $software_record->purchased_channel_id = $purchased_channel->id;
                        }
                        $software_record->save();
                    } else {
                        return $this->response()
                            ->error('缺少必要的字段！');
                    }
                } catch (Exception $exception) {
                    return $this->response()->error($exception->getMessage());
                }
            }
            $return = $this
                ->response()
                ->success('文件导入成功！')
                ->refresh();
        } catch (IOException $e) {
            $return = $this
                ->response()
                ->error('文件读写失败：'.$e->getMessage());
        } catch (UnsupportedTypeException $e) {
            $return = $this
                ->response()
                ->error('不支持的文件类型：'.$e->getMessage());
        } catch (FileNotFoundException $e) {
            $return = $this
                ->response()
                ->error('文件不存在：'.$e->getMessage());
        }

        return $return;
    }

    /**
     * 构造表单.
     */
    public function form()
    {
        $this->file('file', '表格文件')
            ->accept('xls,xlsx,csv')
            ->autoUpload()
            ->uniqueName()
            ->required()
            ->help('导入支持xls、xlsx、csv文件，且表格头必须使用【名称，描述，分类，资产编号，厂商，版本，价格，购入日期，过保日期，购入途径】。');
    }
}
