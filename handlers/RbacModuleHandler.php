<?php

namespace derekisbusy\routes\handlers;

use dektrium\rbac\RbacWebModule;
use yii\base\Event;

class RbacModuleHandler
{
    public static function handleInitEvent($event)
    {
        $module = $event->sender;
        
        if ($module->modelSettings[RbacWebModule::MODEL_PERMISSION] == 'dektrium\rbac\models\Permission') {
            $module->modelSettings[RbacWebModule::MODEL_PERMISSION] = 'derekisbusy\routes\models\Permission';
        }

        // attach menu event
        Event::on(RbacWebModule::className(), RbacWebModule::EVENT_MENU, 
            ['derekisbusy\routes\handlers\MenuHandler', 'handleMenuEvent']);
        
        // attach permission form event
        Event::on(RbacWebModule::className(), RbacWebModule::EVENT_PERMISSION_FORM, 
            ['derekisbusy\routes\handlers\PermissionFormHandler', 'handleFormEvent']);
        
    }
}