<?php

namespace derekisbusy\routes\handlers;

use Yii;

class MenuHandler
{
    public static function handleMenuEvent($event)
    {
        $event->items +=
           
            [
                'label'   => Yii::t('rbac', 'Routes'),
                'url'     => ['/routes'],
            ]
        ;
        
    }
}