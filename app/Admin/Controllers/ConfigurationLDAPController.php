<?php

namespace App\Admin\Controllers;

use Adldap\Auth\BindException;
use Adldap\Auth\PasswordRequiredException;
use Adldap\Auth\UsernameRequiredException;
use App\Admin\Forms\ConfigurationLDAPForm;
use App\Http\Controllers\Controller;
use App\Support\LDAP;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Tab;

class ConfigurationLDAPController extends Controller
{
    /**
     * 页面.
     * @param Content $content
     * @return Content
     */
    public function index(Content $content): Content
    {
        return $content
            ->title(admin_trans_label('LDAP'))
            ->description(trans('admin.list'))
            ->body(function (Row $row) {
                $tab = new Tab();
                $tab->addLink(admin_trans_label('configuration-platform.platform'), route('configurations.platform.index'));
                $tab->addLink(admin_trans_label('configuration-extension.extensions'), route('configurations.extensions.index'));
                $tab->add(admin_trans_label('configuration-l-d-a-p.ldap'), new Card(new ConfigurationLDAPForm()), true);
                $row->column(12, $tab);
            });
    }

    /**
     * AD登录验证
     * @return bool|string
     */
    public function test()
    {
        try {
            if (! admin_setting('ad_enabled')) {
                return -3;
            }

            return LDAP::auth();
        } catch (BindException $e) {
            return $e->getMessage();
        } catch (PasswordRequiredException $e) {
            return -1;
        } catch (UsernameRequiredException $e) {
            return -2;
        }
    }
}
