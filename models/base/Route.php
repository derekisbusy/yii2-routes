<?php

namespace derekisbusy\routes\models\base;

use derekisbusy\routes\models\Permission;
use kartik\tree\TreeView;
use Yii;

/**
 * This is the base model class for table "{{%route}}".
 *
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $lvl
 * @property string $name
 * @property string $route
 * @property string $icon
 * @property integer $icon_type
 * @property integer $active
 * @property integer $status
 * @property integer $selected
 * @property integer $disabled
 * @property integer $readonly
 * @property integer $visible
 * @property integer $collapsed
 * @property integer $movable_u
 * @property integer $movable_d
 * @property integer $movable_l
 * @property integer $movable_r
 * @property integer $removable
 * @property integer $removable_all
 */
class Route extends \kartik\tree\models\Tree
{
    const STATUS_PUBLIC = 0;
    const STATUS_PROTECTED = 1;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['root', 'lft', 'rgt', 'lvl', 'icon_type', 'status', 'selected', 'disabled', 'readonly', 'visible', 'collapsed', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'removable_all'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 60],
            [['icon','route'], 'string', 'max' => 255],
            ['active', 'boolean']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%route}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rbac', 'ID'),
            'root' => Yii::t('rbac', 'Root'),
            'lft' => Yii::t('rbac', 'Lft'),
            'rgt' => Yii::t('rbac', 'Rgt'),
            'lvl' => Yii::t('rbac', 'Lvl'),
            'name' => Yii::t('rbac', 'Name'),
            'route' => Yii::t('rbac', 'Route'),
            'icon' => Yii::t('rbac', 'Icon'),
            'icon_type' => Yii::t('rbac', 'Icon Type'),
            'status' => Yii::t('rbac', 'Status'),
            'active' => Yii::t('rbac', 'Active'),
            'selected' => Yii::t('rbac', 'Selected'),
            'disabled' => Yii::t('rbac', 'Disabled'),
            'readonly' => Yii::t('rbac', 'Readonly'),
            'visible' => Yii::t('rbac', 'Visible'),
            'collapsed' => Yii::t('rbac', 'Collapsed'),
            'movable_u' => Yii::t('rbac', 'Movable U'),
            'movable_d' => Yii::t('rbac', 'Movable D'),
            'movable_l' => Yii::t('rbac', 'Movable L'),
            'movable_r' => Yii::t('rbac', 'Movable R'),
            'removable' => Yii::t('rbac', 'Removable'),
            'removable_all' => Yii::t('rbac', 'Removable All'),
        ];
    }

    /**
     * @inheritdoc
     * @return \derekisbusy\routes\models\RouteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \derekisbusy\routes\models\RouteQuery(get_called_class());
    }
    
//    public function beforeValidate()
//    {
//        if (empty($this->route)) {
//            $this->route = $this->generateRoute();
//        }
//        return parent::beforeValidate();
//    }
    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            
            switch ($this->status)
            {
                case self::STATUS_PUBLIC:
                    $this->icon_type = TreeView::ICON_CSS;
                    $this->icon = 'eye-open';
                    break;
                case self::STATUS_PROTECTED:
                    $this->icon_type = TreeView::ICON_CSS;
                    $this->icon = 'lock';
                    break;
            }
            return true;
        } else {
            return false;
        }
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        $this->saveStatus();
        
        return parent::afterSave($insert, $changedAttributes);
        
    }
    
    public function saveStatus()
    {
        $nodes = $this->children()->all();
        foreach($nodes as $node) {
            $node->status = $this->status;
            $node->save();
        }
    }
    
    public function generateRoute()
    {
        $route = "";
        foreach ($this->parents()->all() as $parent) {
            $route .= $parent->name .'/';
        }
        return $route.$this->name;
    }
    
    public function getItems()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])
            ->viaTable('route_item', ['route_id' => 'id']);
    }
    
    public function getPermissions()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])
            ->where(['type' => \yii\rbac\Item::TYPE_PERMISSION])
            ->viaTable('route_item', ['route_id' => 'id']);
    }
}
