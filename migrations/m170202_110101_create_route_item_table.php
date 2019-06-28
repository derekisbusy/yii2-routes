<?php

use yii\db\Schema;

class m170202_110101_create_route_item_table extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%route_item}}', [
            'item_name' => $this->string(64)->notNull(),
            'route_id' => $this->integer(11)->notNull(),
            ], $tableOptions);
        
        
        // Item
        $this->createIndex(
            'idx-route_item-item_name',
            '{{%route_item}}',
            'item_name'
        );
        
        $this->addForeignKey(
            'fk-route_item-item_name',
            '{{%route_item}}',
            'item_name',
            '{{%auth_item}}',
            'name',
            'CASCADE',
            'CASCADE'
        );
        
        // Route
        $this->createIndex(
            'idx-route_item-route_id',
            '{{%route_item}}',
            'route_id'
        );
        
        $this->addForeignKey(
            'fk-route_item-route_id',
            '{{%route_item}}',
            'route_id',
            '{{%route}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->dropTable('{{%route_item}}');
        $this->execute('SET foreign_key_checks = 1');
    }
}
