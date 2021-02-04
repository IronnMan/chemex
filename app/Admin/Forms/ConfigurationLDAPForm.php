<?php

namespace App\Admin\Forms;

use Dcat\Admin\Widgets\Form;

class ConfigurationLDAPForm extends Form
{
    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        admin_setting($input);

        return $this
            ->response()
            ->success('LDAP配置更新成功！')
            ->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->switch('ad_enabled')
            ->default(admin_setting('ad_enabled'));
        $this->divider();
        $this->text('ad_host_primary')
            ->help('域控主机的域名地址或者ip地址。')
            ->required()
            ->default(admin_setting('ad_host_primary'));
        $this->text('ad_host_secondary')
            ->help('域控主机的域名地址或者ip地址，这是一个备用地址，如果主域控无法连接，将自动选择备域控。')
            ->default(admin_setting('ad_host_secondary'));
        $this->number('ad_port_primary')
            ->help('主域控服务的端口号。')
            ->max(65535)
            ->required()
            ->default(admin_setting('ad_port_primary'));
        $this->number('ad_port_secondary')
            ->help('备域控服务的端口号。')
            ->max(65535)
            ->default(admin_setting('ad_port_secondary'));
        $this->text('ad_base_dn')
            ->help('域控的起始域名，格式请按照 dc=corp,dc=acme,dc=org 填写。')
            ->required()
            ->default(admin_setting('ad_base_dn'));
        $this->text('ad_username')
            ->required()
            ->default(admin_setting('ad_username'));
        $this->password('ad_password')
            ->required()
            ->default(admin_setting('ad_password'));
        $this->switch('ad_use_ssl')
            ->default(admin_setting('ad_use_ssl'));
        $this->switch('ad_use_tls')
            ->default(admin_setting('ad_use_tls'));
        $this->divider();
        $this->switch('ad_login')
            ->help('域登录将优先于传统登录。')
            ->default(admin_setting('ad_login'));
        $this->email('ad_bind_administrator')
            ->help('指定的域账户将作为咖啡壶的管理员角色。')
            ->required()
            ->default(admin_setting('ad_bind_administrator'));
        $this->html(function () {
            return <<<'HTML'
<a class='btn btn-success' style='color: #FFFFFF;' onclick="test()">测试连接（请先保存配置）</a>
<script>
function test(){
    $.ajax({
            url: '/ldap/test',
            success: function (res) {
                res *=1;
                switch (res){
                    case 1:
                        Dcat.success('连接LDAP服务器验证成功！');
                    break;
                    case -1:
                        Dcat.error('缺失必要的密码！');
                    break;
                    case -2:
                        Dcat.error('缺失必要的用户名！');
                    break;
                    case -3:
                        Dcat.error('LDAP未启用！');
                    break;
                    default:
                        Dcat.error('连接失败，请确认配置信息！');
                }
            },
            error: function (res) {
                console.log(res);
                Dcat.error('执行错误：' + res);
            }
        });
}
</script>
HTML;
        })->help('请先保存配置后进行连接测试。');
    }
}
