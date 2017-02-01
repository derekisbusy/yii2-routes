<?php

namespace derekisbusy\routes\models;

use dektrium\rbac\models\Permission as BasePermission;

use yii\helpers\ArrayHelper;

class Permission extends BasePermission
{
    public $id;
    
    public $route_ids = [];
    
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['name', 'description', 'children', 'rule', 'data', 'route_ids'],
            'update' => ['name', 'description', 'children', 'rule', 'data', 'route_ids'],
        ];
    }

    
//    public function getRoutes()
//    {
//        return $this->hasMany(Route::className(), ['id' => 'route_id'])
//            ->viaTable(self::tableName(), ['item_id' => 'id']);
//    }
    
    public function afterSave($insert)
    {
//        parent::afterSave($insert, $changedAttributes);
        if (!$insert) {
            \Yii::$app
                ->db
                ->createCommand()
                ->delete(base\RouteItem::tableName(), ['item_name' => $this->name])
                ->execute();
        }
        $route_ids = explode(",", $this->route_ids[0]);
        foreach ($route_ids as $route_id) {
            $link = new base\RouteItem;
            $link->item_name = $this->name;
            $link->route_id = $route_id;
            if (!$link->save()){
                throw new Exception("Couldn't save related data.");
            }
        }
    }
    
    public function getRouteIds()
    {
        $ids = base\RouteItem::find()->where(['item_name' => $this->name])->asArray()->all();
        return ArrayHelper::getColumn($ids ,'route_id');
    }
}