<?php

use yii\db\Schema;

class m170202_110101_create_route_table extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%route}}', [
            'id' => $this->primaryKey(),
            'root' => $this->integer(11),
            'lft' => $this->integer(11)->notNull(),
            'rgt' => $this->integer(11)->notNull(),
            'lvl' => $this->smallInteger(5)->notNull(),
            'name' => $this->string(60)->notNull(),
            'route' => $this->string(255),
            'icon' => $this->string(255),
            'icon_type' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'active' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'status' => $this->integer(1)->notNull()->defaultValue(1),
            'selected' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'disabled' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'readonly' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'visible' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'collapsed' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'movable_u' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'movable_d' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'movable_l' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'movable_r' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'removable' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'removable_all' => $this->smallInteger(1)->notNull()->defaultValue(1),
            ], $tableOptions);
            
        
        // root
        $this->createIndex(
            'idx-route-root',
            '{{%route}}',
            'root'
        );

        // lft
        $this->createIndex(
            'idx-route-lft',
            '{{%route}}',
            'lft'
        );

        // rgt
        $this->createIndex(
            'idx-route-rgt',
            '{{%route}}',
            'rgt'
        );

        // lvl
        $this->createIndex(
            'idx-route-lvl',
            '{{%route}}',
            'lvl'
        );

        // lft
        $this->createIndex(
            'idx-route-active',
            '{{%route}}',
            'active'
        );
        
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->dropTable('{{%route}}');
        $this->execute('SET foreign_key_checks = 1');
    }
}
