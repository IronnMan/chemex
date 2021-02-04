<?php

use App\Admin\Controllers\CheckRecordController;
use App\Admin\Controllers\DepreciationRuleController;
use App\Admin\Controllers\DeviceCategoryController;
use App\Admin\Controllers\DeviceRecordController;
use App\Admin\Controllers\NotificationController;
use App\Admin\Controllers\PartCategoryController;
use App\Admin\Controllers\PurchasedChannelController;
use App\Admin\Controllers\StaffDepartmentController;
use App\Admin\Controllers\StaffRecordController;
use App\Admin\Controllers\VendorRecordController;
use Dcat\Admin\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');

    $router->get('/ldap/test', 'ConfigurationLDAPController@test')->name('ldap.test');

    /**
     * 辅助信息.
     */
    $router->get('/version', 'VersionController@index');
    $router->get('/version/remote', 'VersionController@getRemoteVersion')
        ->name('remote');
    $router->get('/version/migrate', 'VersionController@migrate')
        ->name('migrate');
    $router->get('/version/clear', 'VersionController@clear')
        ->name('clear');

    /**
     * 配置.
     */
    $router->get('/configurations/platform', 'ConfigurationPlatformController@index')
        ->name('configurations.platform.index');
    $router->resource('/configurations/extensions', 'ConfigurationExtensionController', ['names' => [
        'index' => 'configurations.extensions.index',
    ]]);
    $router->get('/configurations/ldap', 'ConfigurationLDAPController@index')
        ->name('configurations.ldap.index');

    /**
     * 工具.
     */
    $router->get('/tools/qrcode_generator', 'ToolQRCodeGeneratorController@index')
        ->name('qrcode_generator');
    $router->get('/tools/chemex_app', 'ToolChemexAppController@index')
        ->name('chemex_app');

    /**
     * 测试.
     */
    $router->get('/test', 'HomeController@test');

    /**
     * 设备管理.
     */
    $router->resource('/device/records', 'DeviceRecordController', ['names' => [
        'index' => 'device.records.index',
        'show' => 'device.records.show',
        'create' => 'device.records.create',
    ]]);
    $router->resource('/device/tracks', 'DeviceTrackController', ['names' => [
        'index' => 'device.tracks.index',
        'show' => 'device.tracks.show',
    ]]);
    $router->resource('/device/categories', 'DeviceCategoryController', ['names' => [
        'index' => 'device.categories.index',
        'show' => 'device.categories.show',
        'create' => 'device.categories.create',
    ]]);
    $router->get('/selection/device/records', [DeviceRecordController::class, 'selectList'])
        ->name('selection.device.records');
    $router->get('/selection/device/categories', [DeviceCategoryController::class, 'selectList'])
        ->name('selection.device.categories');

    /**
     * 配件管理.
     */
    Route::resource('/part/records', 'PartRecordController', ['names' => [
        'index' => 'part.records.index',
        'show' => 'part.records.show',
    ]]);
    Route::resource('/part/tracks', 'PartTrackController', ['names' => [
        'index' => 'part.tracks.index',
        'show' => 'part.tracks.show',
    ]]);
    Route::resource('/part/categories', 'PartCategoryController', ['names' => [
        'index' => 'part.categories.index',
        'show' => 'part.categories.show',
        'create' => 'part.categories.create',
    ]]);
    Route::get('/selection/part/categories', [PartCategoryController::class, 'selectList'])
        ->name('selection.part.categories');

    /**
     * 软件管理.
     */
    Route::resource('/software/records', 'SoftwareRecordController', ['names' => [
        'index' => 'software.records.index',
        'show' => 'software.records.show',
    ]]);
    Route::resource('/software/categories', 'SoftwareCategoryController', ['names' => [
        'index' => 'software.categories.index',
        'show' => 'software.categories.show',
    ]]);
    Route::resource('/software/tracks', 'SoftwareTrackController', ['names' => [
        'index' => 'software.tracks.index',
        'show' => 'software.tracks.show',
    ]]);
    Route::get('/selection/software/categories', ['SoftwareCategoryController', 'selectList'])
        ->name('selection.software.categories');
    Route::get('/export/software/{software_id}/history', ['SoftwareRecordController', 'exportHistory'])
        ->name('export.software.history');

    /**
     * 服务管理.
     */
    Route::resource('/service/records', 'ServiceRecordController', ['names' => [
        'index' => 'service.records.index',
        'show' => 'service.records.show',
    ]]);
    Route::resource('/service/issues', 'ServiceIssueController', ['names' => [
        'index' => 'service.issues.index',
    ]]);
    Route::resource('/service/tracks', 'ServiceTrackController', ['names' => [
        'index' => 'service.tracks.index',
    ]]);

    /**
     * 耗材管理.
     */
    Route::resource('/consumable/records', 'ConsumableRecordController', ['names' => [
        'index' => 'consumable.records.index',
        'show' => 'consumable.records.show',
    ]]);
    Route::resource('/consumable/categories', 'ConsumableCategoryController', ['names' => [
        'index' => 'consumable.categories.index',
        'show' => 'consumable.categories.show',
    ]]);
    Route::resource('/consumable/tracks', 'ConsumableTrackController', ['names' => [
        'index' => 'consumable.tracks.index',
        'show' => 'consumable.tracks.show',
    ]]);

    /**
     * 待办.
     */
    Route::resource('/todo/records', 'TodoRecordController', ['names' => [
        'index' => 'todo.records.index',
        'show' => 'todo.records.show',
    ]]);

    /**
     * 厂商管理.
     */
    $router->resource('/vendor/records', 'VendorRecordController', ['names' => [
        'create' => 'vendor.records.create',
    ]]);
    $router->get('/selection/vendor/records', [VendorRecordController::class, 'selectList'])
        ->name('selection.vendor.records');

    /**
     * 购入途径管理.
     */
    $router->resource('/purchased/channels', 'PurchasedChannelController', ['names' => [
        'create' => 'purchased.channels.create',
    ]]);
    $router->get('/selection/purchased/channels', [PurchasedChannelController::class, 'selectList'])
        ->name('selection.purchased.channels');

    /**
     * 组织管理.
     */
    $router->resource('/staff/records', 'StaffRecordController', ['names' => [
        'index' => 'staff.records.index',
    ]]);
    $router->resource('/staff/departments', 'StaffDepartmentController', ['names' => [
        'index' => 'staff.departments.index',
    ]]);
    $router->get('/selection/staff/records', [StaffRecordController::class, 'selectList'])
        ->name('selection.staff.records');
    $router->get('/selection/staff/departments', [StaffDepartmentController::class, 'selectList'])
        ->name('selection.staff.departments');

    /**
     * 盘点管理.
     */
    $router->resource('/check/records', 'CheckRecordController', ['names' => [
        'index' => 'check.records.index',
        'show' => 'check.records.show',
    ]]);
    $router->resource('/check/tracks', 'CheckTrackController', ['names' => [
        'index' => 'check.tracks.index',
        'show' => 'check.tracks.show',
    ]]);

    /**
     * 故障维护.
     */
    $router->resource('/maintenance/records', 'MaintenanceRecordController');

    /**
     * 折旧规则.
     */
    $router->resource('/depreciation/rules', 'DepreciationRuleController', ['names' => [
        'create' => 'depreciation.rules.create',
    ]]);
    $router->get('/selection/depreciation/rules', [DepreciationRuleController::class, 'selectList'])
        ->name('selection.depreciation.rules');

    /**
     * 导出.
     *///TODO
    $router->get('/export/device/{device_id}/history', [DeviceRecordController::class, 'exportHistory'])
        ->name('export.device.history');
    $router->get('/export/check/{check_id}/report', [CheckRecordController::class, 'exportReport'])
        ->name('export.check.report');

    /**
     * 通知.
     */
    $router->get('/notifications/read_all', [NotificationController::class, 'readAll'])
        ->name('notification.read.all');
    $router->get('/notifications/read/{id}', [NotificationController::class, 'read'])
        ->name('notification.read');
});
