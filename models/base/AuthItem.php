<?php

namespace derekisbusy\routes\models\base;

use Yii;

/**
 * This is the base model class for table "{{%auth_item}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property resource $data
 * @property integer $created_at
 * @property integer $updated_at
 */
class AuthItem extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name'], 'string', 'max' => 60],
            [['rule_name'], 'string', 'max' => 64]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_item}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('routes', 'ID'),
            'name' => Yii::t('routes', 'Name'),
            'type' => Yii::t('routes', 'Type'),
            'description' => Yii::t('routes', 'Description'),
            'rule_name' => Yii::t('routes', 'Rule Name'),
            'data' => Yii::t('routes', 'Data'),
        ];
    }

    /**
     * @inheritdoc
     * @return \derekisbusy\routes\models\AuthItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \derekisbusy\routes\models\AuthItemQuery(get_called_class());
    }
    
    public function getChildren()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'child'])
            ->viaTable('auth_item_child', ['parent' => 'name']);
    }
    
    
    public function getParents()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'parent'])
            ->viaTable('auth_item_child', ['child' => 'name']);
    }
    
    public function getPermissions()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'child'])
            ->where(['type' => \yii\rbac\Item::TYPE_PERMISSION])
            ->viaTable('auth_item_child', ['parent' => 'name']);
    }
    
    public function getRoles()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'child'])
            ->where(['type' => \yii\rbac\Item::TYPE_ROLE])
            ->viaTable('auth_item_child', ['parent' => 'name']);
    }
}
