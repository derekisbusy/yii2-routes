<?php

namespace derekisbusy\routes\models\base;

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
 * @property string $icon
 * @property integer $icon_type
 * @property integer $active
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['root', 'lft', 'rgt', 'lvl', 'icon_type', 'active', 'selected', 'disabled', 'readonly', 'visible', 'collapsed', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'removable_all'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 60],
            [['icon'], 'string', 'max' => 255]
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
            'icon' => Yii::t('rbac', 'Icon'),
            'icon_type' => Yii::t('rbac', 'Icon Type'),
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
     * @return \derekisbusy\rbacroutes\models\RouteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \derekisbusy\rbacroutes\models\RouteQuery(get_called_class());
    }
}
