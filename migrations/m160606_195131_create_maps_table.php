<?php

use yii\db\Migration;

class m160606_195131_create_maps_table extends Migration
{
    public function up()
    {
//        $this->createTable('maps_table', [
//            'id' => $this->primaryKey()
//        ]);
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%maps}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'name' => $this->string(30)->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'control_points_number' => $this->integer(),
            'root_route_length' => $this->integer(),
            'map_scale' => $this->integer(),
            'file_name' => $this->string(),
        ], $tableOptions);

        $this->createIndex('FK_map_author', '{{%maps}}', 'author_id');
        $this->addForeignKey(
            'FK_map_author', '{{%maps}}', 'author_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%maps}}');
    }
}
