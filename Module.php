<?php

namespace derekisbusy\routes;

use dektrium\rbac\RbacWebModule;
use yii\base\Event;

/**
 * routes module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'derekisbusy\routes\controllers';
    
    /**
     * @inheritdoc
     */
    public $defaultRoute = 'route';

    public $apps = [];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // attach menu event
        Event::on(self::className(), RbacWebModule::EVENT_MENU, 
            ['derekisbusy\routes\handlers\MenuHandler', 'handleMenuEvent']);
        
    }
    
}
