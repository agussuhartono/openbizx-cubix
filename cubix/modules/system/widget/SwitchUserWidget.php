<?php

/**
 * Openbiz Cubi Application Platform
 *
 * LICENSE http://code.google.com/p/openbiz-cubi/wiki/CubiLicense
 *
 * @package   cubi.system.widget
 * @copyright Copyright (c) 2005-2011, Openbiz Technology LLC
 * @license   http://code.google.com/p/openbiz-cubi/wiki/CubiLicense
 * @link      http://code.google.com/p/openbiz-cubi/
 * @version   $Id: SwitchUserWidget.php 5075 2013-01-07 09:20:55Z hellojixian@gmail.com $
 */

use Openbiz\Openbiz;
use Openbiz\Easy\EasyForm;

class SwitchUserWidget extends EasyForm
{

    public $showWidget = false;

    public function fetchData()
    {
        if ($this->processUserInit()) {
            return;
        }

        if (!Openbiz::$app->allowUserAccess('Session.Switch_Session')) {
            $this->showWidget = false;
            if (!Openbiz::$app->getSessionContext()->getVar("_PREV_USER_PROFILE")) {
                return;
            } else {
                $this->showWidget = true;
            }
        } else {
            $this->showWidget = true;
        }
        $record['username'] = Openbiz::$app->getUserProfile("username");
        return $record;
    }

    public function processUserInit()
    {
        $prefService = Openbiz::getService(OPENBIZ_PREFERENCE_SERVICE);
        $userId = Openbiz::$app->getUserProfile("Id");
        $currentView = $this->getWebpageObject()->objectName;
        if ($currentView != 'myaccount.view.ResetPasswordView' && !isset($_GET['force']) && (int) $prefService->getPreference("force_change_passwd") == 1) {

            Openbiz::$app->getClientProxy()->redirectPage(OPENBIZ_APP_INDEX_URL . '/myaccount/reset_password/force');
            return true;
        }

        if ($currentView != 'myaccount.view.MyProfileView' && !isset($_GET['force']) && (int) $prefService->getPreference("force_complete_profile") == 1) { {
                Openbiz::$app->getClientProxy()->redirectPage(OPENBIZ_APP_INDEX_URL . '/myaccount/my_profile/force');
                return true;
            }
        }
        return false;
    }

    public function SwitchSession()
    {
        if (!Openbiz::$app->allowUserAccess('Session.Switch_Session')) {
            if (!Openbiz::$app->getSessionContext()->getVar("_PREV_USER_PROFILE")) {
                return;
            }
        }

        $data = $this->readInputRecord();
        $username = $data['username'];

        if (!$username) {
            return;
        }

        $serviceObj = Openbiz::getService(PROFILE_SERVICE);

        if (method_exists($serviceObj, 'SwitchUserProfile')) {
            $serviceObj->SwitchUserProfile($username);
        }
        Openbiz::$app->getClientProxy()->runClientScript("<script>window.location.reload();</script>");
    }

    public function outputAttrs()
    {
        $output = parent::outputAttrs();
        $output['show_widget'] = $this->showWidget;
        return $output;
    }

}

