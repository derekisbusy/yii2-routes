<?php

namespace derekisbusy\routes\models\base;

use Yii;

/**
 * This is the base model class for table "{{%route_item}}".
 *
 * @property integer $item_id
 * @property integer $route_id
 *
 * @property \derekisbusy\routes\models\AuthItem $item
 * @property \derekisbusy\routes\models\Route $route
 */
class RouteItem extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_name', 'route_id'], 'required'],
            [['route_id'], 'integer'],
            [['item_name'], 'string', 'length' => [1,60]],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%route_item}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_name' => Yii::t('routes', 'Item Name'),
            'route_id' => Yii::t('routes', 'Route ID'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(\derekisbusy\routes\models\AuthItem::className(), ['name' => 'item_name']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoute()
    {
        return $this->hasOne(\derekisbusy\routes\models\Route::className(), ['id' => 'route_id']);
    }
    
    /**
     * @inheritdoc
     * @return \derekisbusy\rbacroutes\models\RouteItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \derekisbusy\routes\models\RouteItemQuery(get_called_class());
    }
}
