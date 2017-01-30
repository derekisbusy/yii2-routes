<?php

namespace derekisbusy\routes;

use derekisbusy\routes\models\base\Route as Route2;
use derekisbusy\routes\models\Route;
use ReflectionClass;
use Yii;
use yii\helpers\VarDumper;

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

    public $apps = [
        
            '@backend' => null,
            '@frontend' => [
                'modules' =>[
                    'patient' => [
                        'class' => 'derekisbusy\medical\frontend\modules\patient\Module',
                    ],
                ]
            ]
    ];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
    
    
    /**
     * Get available and assigned routes
     * @return array
     */
    public function getRoutes()
    {
        \Yii::$app
            ->db
            ->createCommand()
            ->delete(Route2::tableName(), ['readonly' => 1])
            ->execute();
        return $this->getAppRoutes();
    }

    /**
     * Get list of application routes
     * @return array
     */
    public function getAppRoutes($module = null, $app = null, $root = null)
    {
        $apps = $this->apps;
        if ($module === null) {
            if (!empty($apps)) {
                $result = [];
                foreach ($apps as $alias => $config) {
//                    var_dump($root->getIsNewRecord()); return;
                    if ($config === null) {
                        $root = new Route;
                        $root->name = $alias;
                        $root->route = $alias;
                        $root->status = Route::STATUS_PROTECTED;
                        $root->readonly = 1;
                        $root->makeRoot();
                        $result += $this->getAppRoutes(Yii::$app, $alias, $root);
                    } else {
                        $m = new yii\base\Module($alias, null, $config);
                        $result += $this->getAppRoutes($m, true, null);
                    }
                    
                }
//                        var_dump($result); exit;
                return $result;
            }
            $module = Yii::$app;
        } elseif (is_string($module)) {
            $module = Yii::$app->getModule($module);
        }
//        $key = [__METHOD__, $module->getUniqueId()];
//        $cache = $this->cache;
//        if ($cache === null || ($result = $cache->get($key)) === false) {
            $result = [];
//            if (!$root) {
//                $root = new Route;
//                $app = "@app";
//                $root->name = $app;
//                $root->route = $app;
//                $root->makeRoot();
//                return;
//            }
            $this->getRouteRecursive($module, $result, $app, $root);
//            if ($cache !== null) {
//                $cache->set($key, $result, Configs::instance()->cacheDuration, new TagDependency([
//                    'tags' => self::CACHE_TAG,
//                ]));
//            }
//        }

        return $result;
    }

    /**
     * Get route(s) recursive
     * @param \yii\base\Module $module
     * @param array $result
     */
    protected function getRouteRecursive($module, &$result, &$app= null, $parent = null)
    {
        $token = "Get Route of '" . get_class($module) . "' with id '" . $module->uniqueId . "'";
//        Yii::beginProfile($token, __METHOD__);
//        try {
            
            if ($app===true) {
                $all = ltrim($module->uniqueId . '/*', '/');
            } elseif (is_string($app) || is_null($app)) {
                $all = $app.'/' . ltrim($module->uniqueId . '/*', '/');
            }
            $result[$all] = $all;
            
            if ($module->uniqueId) {
                $name = explode("/", $module->uniqueId);
                $name = array_pop($name);
                $route = new Route;
                $route->name = $name;
                $route->route = $all;
                $route->status = Route::STATUS_PROTECTED;
                $route->readonly = 1;
                if ($parent) {
//                    var_dump($parent);
                    $route->appendTo($parent);
                } else {
                    $route->makeRoot();
                }
            }
            else {
                $route = $parent;
            }
            
            
            foreach ($module->getModules() as $id => $child) {
                if (($child = $module->getModule($id)) !== null) {
//                    var_dump($id);
                    $this->getRouteRecursive($child, $result, $app, $route);
                }
            }

            foreach ($module->controllerMap as $id => $type) {
                $this->getControllerActions($type, $id, $module, $result, $app, $route);
            }

            $namespace = trim($module->controllerNamespace, '\\') . '\\';
            $this->getControllerFiles($module, $namespace, '', $result, $app, $route);
            
            
//        } catch (Exception $exc) {
//            Yii::error($exc->getMessage(), __METHOD__);
//        }
//        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get list controller under module
     * @param \yii\base\Module $module
     * @param string $namespace
     * @param string $prefix
     * @param mixed $result
     * @return mixed
     */
    protected function getControllerFiles($module, $namespace, $prefix, &$result, $app = null, $parent = null)
    {
        $path = Yii::getAlias('@' . str_replace('\\', '/', $namespace), false);
        $token = "Get controllers from '$path'";
//        Yii::beginProfile($token, __METHOD__);
//        try {
            if (!is_dir($path)) {
                return;
            }
            foreach (scandir($path) as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (is_dir($path . '/' . $file) && preg_match('%^[a-z0-9_/]+$%i', $file . '/')) {
                    $this->getControllerFiles($module, $namespace . $file . '\\', $prefix . $file . '/', $result, $app, $parent);
                } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                    $baseName = substr(basename($file), 0, -14);
                    $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $baseName));
                    $id = ltrim(str_replace(' ', '-', $name), '-');
                    $className = $namespace . $baseName . 'Controller';
                    if (strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller')) {
                        $this->getControllerActions($className, $prefix . $id, $module, $result, $app, $parent);
                    }
                }
            }
//        } catch (Exception $exc) {
//            Yii::error($exc->getMessage(), __METHOD__);
//        }
//        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get list action of controller
     * @param mixed $type
     * @param string $id
     * @param \yii\base\Module $module
     * @param string $result
     */
    protected function getControllerActions($type, $id, $module, &$result, $app = null, $parent = null)
    {
        $token = "Create controller with cofig=" . VarDumper::dumpAsString($type) . " and id='$id'";
//        Yii::beginProfile($token, __METHOD__);
//        try {
            /* @var $controller \yii\base\Controller */
            $controller = Yii::createObject($type, [$id, $module]);
            if ($app===true) {
                $all = "{$controller->uniqueId}/*";
            } else {
                $all = $app."/{$controller->uniqueId}/*";
            }
            $name = explode("/", $controller->uniqueId);
            $name = array_pop($name);
            $route = new Route;
            $route->name = $name;
            $route->route = $all;
            $route->status = Route::STATUS_PROTECTED;
            $route->readonly = 1;
            $route->appendTo($parent);
            
            $this->getActionRoutes($controller, $result, $app, $route);
            $result[$all] = $all;
            
            
//        } catch (Exception $exc) {
//            Yii::error($exc->getMessage(), __METHOD__);
//        }
//        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get route of action
     * @param \yii\base\Controller $controller
     * @param array $result all controller action.
     */
    protected function getActionRoutes($controller, &$result, &$app = null, $parent = null)
    {
        $token = "Get actions of controller '" . $controller->uniqueId . "'";
//        Yii::beginProfile($token, __METHOD__);
//        try {
            if ($app===true) {
                $prefix = $controller->uniqueId . '/';
            } else {
                $prefix = $app.'/' . $controller->uniqueId . '/';
            }
            foreach ($controller->actions() as $id => $value) {
                $result[$prefix . $id] = $prefix . $id;
                $name = explode("/", $id);
                $name = array_pop($name);
                $route = new Route;
                $route->name = $name;
                $route->route = $prefix . $id;
                $route->status = Route::STATUS_PROTECTED;
                $route->readonly = 1;
                $route->appendTo($parent);
            }
            $class = new ReflectionClass($controller);
            foreach ($class->getMethods() as $method) {
                $name = $method->getName();
                if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                    $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', substr($name, 6)));
                    $name = ltrim(str_replace(' ', '-', $name), '-');
                    $all = $prefix . $name;
                    $result[$all] = $all;
                    $name = explode("/", $name);
                    $name = array_pop($name);
                    $route = new Route;
                    $route->name = $name;
                    $route->route = $all;
                    $route->status = Route::STATUS_PROTECTED;
                    $route->readonly = 1;
                    $route->appendTo($parent);
                }
            }
//        } catch (Exception $exc) {
//            Yii::error($exc->getMessage(), __METHOD__);
//        }
//        Yii::endProfile($token, __METHOD__);
    }
}
