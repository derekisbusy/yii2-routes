<?php

namespace derekisbusy\routes\controllers;

use Yii;
use derekisbusy\routes\models\Route;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RouteController implements the CRUD actions for Route model.
 */
class RouteController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
//            'access' => [
//                'class' => \yii\filters\AccessControl::className(),
//                'rules' => [
//                    [
//                        'allow' => true,
//                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'save-as-new'],
//                        'roles' => ['@']
//                    ],
//                    [
//                        'allow' => false
//                    ]
//                ]
//            ]
        ];
    }

    /**
     * Lists all Route models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Route::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function refreshRoutes()
    {
        
        
        $routes = $this->module->getAppRoutes();
        
        foreach ($routes as $name) {
            $route = new \derekisbusy\routes\models\Route();
            $route->name = $name;
            $route->save();
        }
    }
    
    
    

    /**
     * Saves a node once form is submitted
     */
    public function actionSave()
    {
        $post = Yii::$app->request->post();
        static::checkValidRequest(false, !isset($post['treeNodeModify']));
        $treeNodeModify = $parentKey = $currUrl = $treeSaveHash = null;
        $modelClass = '\kartik\tree\models\Tree';
        $data = static::getPostData();
        extract($data);
        $module = TreeView::module();
        $keyAttr = $module->dataStructure['keyAttribute'];
        /**
         * @var Tree $modelClass
         * @var Tree $node
         * @var Tree $parent
         */
        if ($treeNodeModify) {
            $node = new $modelClass;
            $successMsg = Yii::t('kvtree', 'The node was successfully created.');
            $errorMsg = Yii::t('kvtree', 'Error while creating the node. Please try again later.');
        } else {
            $tag = explode("\\", $modelClass);
            $tag = array_pop($tag);
            $id = $post[$tag][$keyAttr];
            $node = $modelClass::findOne($id);
            $successMsg = Yii::t('kvtree', 'Saved the node details successfully.');
            $errorMsg = Yii::t('kvtree', 'Error while saving the node. Please try again later.');
        }
        $node->activeOrig = $node->active;
        $isNewRecord = $node->isNewRecord;
        $node->load($post);
        if ($treeNodeModify) {
            if ($parentKey == TreeView::ROOT_KEY) {
                $node->makeRoot();
            } else {
                $parent = $modelClass::findOne($parentKey);
                $node->appendTo($parent);
            }
        }
        $errors = $success = false;
        if ($node->save()) {
            // check if active status was changed
            if (!$isNewRecord && $node->activeOrig != $node->active) {
                if ($node->active) {
                    $success = $node->activateNode(false);
                    $errors = $node->nodeActivationErrors;
                } else {
                    $success = $node->removeNode(true, false); // only deactivate the node(s)
                    $errors = $node->nodeRemovalErrors;
                }
            } else {
                $success = true;
            }
            if (!empty($errors)) {
                $success = false;
                $errorMsg = "<ul style='padding:0'>\n";
                foreach ($errors as $err) {
                    $errorMsg .= "<li>" . Yii::t('kvtree', "Node # {id} - '{name}': {error}", $err) . "</li>\n";
                }
                $errorMsg .= "</ul>";
            }
        } else {
            $errorMsg = '<ul style="margin:0"><li>' . implode('</li><li>', $node->getFirstErrors()) . '</li></ul>';
        }
        if (Yii::$app->has('session')) {
            $session = Yii::$app->session;
            $session->set(ArrayHelper::getValue($post, 'nodeSelected', 'kvNodeId'), $node->{$keyAttr});
            if ($success) {
                $session->setFlash('success', $successMsg);
            } else {
                $session->setFlash('error', $errorMsg);
            }
        } elseif (!$success) {
            throw new ErrorException("Error saving node!\n{$errorMsg}");
        }
        return $this->redirect($currUrl);
    }

    /**
     * View, create, or update a tree node via ajax
     *
     * @return string json encoded response
     */
    public function actionManage()
    {
        static::checkValidRequest();
        $callback = function () {
            $parentKey = null;
            $modelClass = '\kartik\tree\models\Tree';
            $isAdmin = $softDelete = $showFormButtons = $showIDAttribute = $allowNewRoots = false;
            $currUrl = $nodeView = $formAction = $nodeSelected = '';
            $formOptions = $iconsList = $nodeAddlViews = $breadcrumbs = [];
            $data = static::getPostData();
            extract($data);
            /**
             * @var Tree $modelClass
             * @var Tree $node
             */
            if (!isset($id) || empty($id)) {
                $node = new $modelClass;
                $node->initDefaults();
            } else {
                $node = $modelClass::findOne($id);
            }
            $module = TreeView::module();
            $params = $module->treeStructure + $module->dataStructure + [
                    'node' => $node,
                    'parentKey' => $parentKey,
                    'action' => $formAction,
                    'formOptions' => empty($formOptions) ? [] : $formOptions,
                    'modelClass' => $modelClass,
                    'currUrl' => $currUrl,
                    'isAdmin' => $isAdmin,
                    'iconsList' => $iconsList,
                    'softDelete' => $softDelete,
                    'showFormButtons' => $showFormButtons,
                    'showIDAttribute' => $showIDAttribute,
                    'allowNewRoots' => $allowNewRoots,
                    'nodeView' => $nodeView,
                    'nodeAddlViews' => $nodeAddlViews,
                    'nodeSelected' => $nodeSelected,
                    'breadcrumbs' => empty($breadcrumbs) ? [] : $breadcrumbs,
                    'noNodesMessage' => ''
                ];
            if (!empty($module->unsetAjaxBundles)) {
                Event::on(
                    View::className(), View::EVENT_AFTER_RENDER, function ($e) use ($module) {
                    foreach ($module->unsetAjaxBundles as $bundle) {
                        unset($e->sender->assetBundles[$bundle]);
                    }
                }
                );
            }
            static::checkSignature('manage', $data);
            return $this->renderAjax($nodeView, ['params' => $params]);
        };
        return self::process(
            $callback,
            Yii::t('kvtree', 'Error while viewing the node. Please try again later.'),
            null
        );
    }

    /**
     * Remove a tree node
     */
    public function actionRemove()
    {
        static::checkValidRequest();
        $callback = function () {
            /**
             * @var Tree $modelClass
             * @var Tree $node
             */
            $id = null;
            $modelClass = '\kartik\tree\models\Tree';
            $softDelete = false;
            $data = static::getPostData();
            static::checkSignature('remove', $data);
            extract($data);
            $node = $modelClass::findOne($id);
            return $node->removeNode($softDelete);
        };
        return self::process(
            $callback,
            Yii::t('kvtree', 'Error removing the node. Please try again later.'),
            Yii::t('kvtree', 'The node was removed successfully.')
        );
    }

    /**
     * Move a tree node
     */
    public function actionMove()
    {
        /**
         * @var Tree $modelClass
         * @var Tree $nodeFrom
         * @var Tree $nodeTo
         */
        static::checkValidRequest();
        $dir = $idFrom = $idTo = $treeMoveHash = null;
        $modelClass = '\kartik\tree\models\Tree';
        $allowNewRoots = false;
        $data = static::getPostData();
        extract($data);
        $nodeFrom = $modelClass::findOne($idFrom);
        $nodeTo = $modelClass::findOne($idTo);
        $isMovable = $nodeFrom->isMovable($dir);
        $errorMsg = $isMovable ? Yii::t('kvtree', 'Error while moving the node. Please try again later.') :
            Yii::t('kvtree', 'The selected node cannot be moved.');
        $callback = function () use ($dir, $nodeFrom, $nodeTo, $allowNewRoots, $treeMoveHash, $isMovable, $data) {
            if (!empty($nodeFrom) && !empty($nodeTo)) {
                static::checkSignature('move', $data);
                if (!$isMovable) {
                    return false;
                }
                if ($dir == 'u') {
                    $nodeFrom->insertBefore($nodeTo);
                } elseif ($dir == 'd') {
                    $nodeFrom->insertAfter($nodeTo);
                } elseif ($dir == 'l') {
                    if ($nodeTo->isRoot() && $allowNewRoots) {
                        $nodeFrom->makeRoot();
                    } else {
                        $nodeFrom->insertAfter($nodeTo);
                    }
                } elseif ($dir == 'r') {
                    $nodeFrom->appendTo($nodeTo);
                }
                return $nodeFrom->save();
            }
            return true;
        };
        return self::process($callback, $errorMsg, Yii::t('kvtree', 'The node was moved successfully.'));
    }

}
