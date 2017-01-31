<?php


namespace derekisbusy\routes\handlers;

class PermissionFormHandler
{
    public static function handleFormEvent($event)
    {
        $event->renderViews[] = '@vendor/derekisbusy/yii2-routes/views/permission/_form';
        
    }
}
