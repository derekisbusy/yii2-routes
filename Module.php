<?php

namespace derekisbusy\routes;

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
//        $manager = Configs::authManager();
//        $routes = $this->getAppRoutes();
//        $exists = [];
//        foreach (array_keys($manager->getPermissions()) as $name) {
//            if ($name[0] !== '/') {
//                continue;
//            }
//            $exists[] = $name;
//            unset($routes[$name]);
//        }
//        return [
//            'available' => array_keys($routes),
//            'assigned' => $exists,
//        ];
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
                    $root = new models\Route;
                    $root->name = $alias;
                    $root->makeRoot();
                    if ($config === null) {
                        $result += $this->getAppRoutes(Yii::$app, $alias, $root);
                    } else {
                        $m = new yii\base\Module($alias, null, $config);
                        $result += $this->getAppRoutes($m, true, $root);
                    }
                    
                }
//                        var_dump($result); exit;
                return $result;
            }
            $module = Yii::$app;
        } elseif (is_string($module)) {
            $module = Yii::$app->getModule($module);
        }
        $key = [__METHOD__, $module->getUniqueId()];
        $cache = $this->cache;
        if ($cache === null || ($result = $cache->get($key)) === false) {
            $result = [];
            $this->getRouteRecursive($module, $result, $app);
//            if ($cache !== null) {
//                $cache->set($key, $result, Configs::instance()->cacheDuration, new TagDependency([
//                    'tags' => self::CACHE_TAG,
//                ]));
//            }
        }

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
        Yii::beginProfile($token, __METHOD__);
        try {
            
            if ($app===true) {
                $all = ltrim($module->uniqueId . '/*', '/');
            } elseif (is_string($app) || is_null($app)) {
                $all = $app.'/' . ltrim($module->uniqueId . '/*', '/');
            }
            $result[$all] = $all;
            
            
            $route = new models\Route;
            $route->name = $all;
            if ($parent) {
                $route->appendTo($parent);
            }
            
            
            foreach ($module->getModules() as $id => $child) {
                if (($child = $module->getModule($id)) !== null) {
                    $this->getRouteRecursive($child, $result, $app);
                }
            }

            foreach ($module->controllerMap as $id => $type) {
                $this->getControllerActions($type, $id, $module, $result, $app);
            }

            $namespace = trim($module->controllerNamespace, '\\') . '\\';
            $this->getControllerFiles($module, $namespace, '', $result, $app);
            
            
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get list controller under module
     * @param \yii\base\Module $module
     * @param string $namespace
     * @param string $prefix
     * @param mixed $result
     * @return mixed
     */
    protected function getControllerFiles($module, $namespace, $prefix, &$result, $app = null)
    {
        $path = Yii::getAlias('@' . str_replace('\\', '/', $namespace), false);
        $token = "Get controllers from '$path'";
        Yii::beginProfile($token, __METHOD__);
        try {
            if (!is_dir($path)) {
                return;
            }
            foreach (scandir($path) as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (is_dir($path . '/' . $file) && preg_match('%^[a-z0-9_/]+$%i', $file . '/')) {
                    $this->getControllerFiles($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
                } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                    $baseName = substr(basename($file), 0, -14);
                    $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $baseName));
                    $id = ltrim(str_replace(' ', '-', $name), '-');
                    $className = $namespace . $baseName . 'Controller';
                    if (strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller')) {
                        $this->getControllerActions($className, $prefix . $id, $module, $result, $app);
                    }
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get list action of controller
     * @param mixed $type
     * @param string $id
     * @param \yii\base\Module $module
     * @param string $result
     */
    protected function getControllerActions($type, $id, $module, &$result, &$app = null)
    {
        $token = "Create controller with cofig=" . VarDumper::dumpAsString($type) . " and id='$id'";
        Yii::beginProfile($token, __METHOD__);
        try {
            /* @var $controller \yii\base\Controller */
            $controller = Yii::createObject($type, [$id, $module]);
            $this->getActionRoutes($controller, $result, $app);
            if ($app===true) {
                $all = "{$controller->uniqueId}/*";
            } else {
                $all = $app."/{$controller->uniqueId}/*";
            }
            $result[$all] = $all;
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get route of action
     * @param \yii\base\Controller $controller
     * @param array $result all controller action.
     */
    protected function getActionRoutes($controller, &$result, &$app = null)
    {
        $token = "Get actions of controller '" . $controller->uniqueId . "'";
        Yii::beginProfile($token, __METHOD__);
        try {
            if ($app===true) {
                $prefix = $controller->uniqueId . '/';
            } else {
                $prefix = $app.'/' . $controller->uniqueId . '/';
            }
            foreach ($controller->actions() as $id => $value) {
                $result[$prefix . $id] = $prefix . $id;
            }
            $class = new \ReflectionClass($controller);
            foreach ($class->getMethods() as $method) {
                $name = $method->getName();
                if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                    $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', substr($name, 6)));
                    $id = $prefix . ltrim(str_replace(' ', '-', $name), '-');
                    $result[$id] = $id;
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }
}
