<?php

/**
 * Openbiz Cubi Application Platform
 *
 * LICENSE http://code.google.com/p/openbiz-cubi/wiki/CubiLicense
 *
 * @package   cubi.myaccount.form
 * @copyright Copyright (c) 2005-2011, Openbiz Technology LLC
 * @license   http://code.google.com/p/openbiz-cubi/wiki/CubiLicense
 * @link      http://code.google.com/p/openbiz-cubi/
 * @version   $Id: ProfileForm.php 5075 2013-01-07 09:20:55Z hellojixian@gmail.com $
 */
use Openbiz\Openbiz;

include_once(Openbiz::$app->getModulePath() . "/contact/form/ContactForm.php");

class ProfileForm extends ContactForm
{

    public function allowAccess()
    {
        parent::allowAccess();

        if (Openbiz::$app->getUserProfile("Id")) {
            return 1;
        } else {
            return 0;
        }
    }

    public function _doUpdate($inputRecord, $currentRecord)
    {
        $result = parent::_doUpdate($inputRecord, $currentRecord);
        if ($this->getWebpageObject()->isForceCompeleteProfile()) {
            Openbiz::getService(OPENBIZ_PREFERENCE_SERVICE)->setPreference('force_complete_profile', 0);
        }
        return $result;
    }

    protected function processPostAction()
    {
        if ($this->getWebpageObject()->isForceCompeleteProfile()) {
            $profileDefaultPageArr = Openbiz::$app->getUserProfile('roleStartpage');
            $pageURL = OPENBIZ_APP_INDEX_URL . $profileDefaultPageArr[0];
            Openbiz::$app->getClientProxy()->redirectPage($pageURL);
            return;
        }
        return parent::processPostAction();
    }

    public function fetchData()
    {
        $svcobj = Openbiz::getService(PROFILE_SERVICE);
        //echo Openbiz::$app->getUserProfile("profile_Id");
        //echo $svcobj->checkExist(Openbiz::$app->getUserProfile("profile_Id"));
        if (!Openbiz::$app->getUserProfile("profile_Id") || !$svcobj->checkExist(Openbiz::$app->getUserProfile("profile_Id"))) {
            $profile_id = $svcobj->CreateProfile();
            $svcobj->InitProfile(Openbiz::$app->getUserProfile("username"));
            $this->updateForm();
        }
        return parent::fetchData();
    }

}
