<?php

namespace derekisbusy\routes\models;

use Exception;
use derekisbusy\routes\Configs;
use mdm\admin\components\Helper;
use mdm\admin\components\RouteRule;
use Yii;
use yii\caching\TagDependency;
use yii\helpers\VarDumper;

class Route extends \derekisbusy\routes\models\base\Route
{
    /**
     * Assign or remove items
     * @param array $routes
     * @return array
     */
    public function addNew($routes)
    {
        $manager = Configs::authManager();
        foreach ($routes as $route) {
            try {
                $r = explode('&', $route);
                $item = $manager->createPermission('/' . trim($route, '/'));
                if (count($r) > 1) {
                    $action = '/' . trim($r[0], '/');
                    if (($itemAction = $manager->getPermission($action)) === null) {
                        $itemAction = $manager->createPermission($action);
                        $manager->add($itemAction);
                    }
                    unset($r[0]);
                    foreach ($r as $part) {
                        $part = explode('=', $part);
                        $item->data['params'][$part[0]] = isset($part[1]) ? $part[1] : '';
                    }
                    $this->setDefaultRule();
                    $item->ruleName = RouteRule::RULE_NAME;
                    $manager->add($item);
                    $manager->addChild($item, $itemAction);
                } else {
                    $manager->add($item);
                }
            } catch (Exception $exc) {
                Yii::error($exc->getMessage(), __METHOD__);
            }
        }
        Helper::invalidate();
    }

    /**
     * Assign or remove items
     * @param array $routes
     * @return array
     */
    public function remove($routes)
    {
        $manager = Configs::authManager();
        foreach ($routes as $route) {
            try {
                $item = $manager->createPermission('/' . trim($route, '/'));
                $manager->remove($item);
            } catch (Exception $exc) {
                Yii::error($exc->getMessage(), __METHOD__);
            }
        }
        Helper::invalidate();
    }


    /**
     * Ivalidate cache
     */
    public static function invalidate()
    {
        if (Configs::cache() !== null) {
            TagDependency::invalidate(Configs::cache(), self::CACHE_TAG);
        }
    }

    /**
     * Set default rule of parameterize route.
     */
    protected function setDefaultRule()
    {
        if (Configs::authManager()->getRule(RouteRule::RULE_NAME) === null) {
            Configs::authManager()->add(new RouteRule());
        }
    }
}
