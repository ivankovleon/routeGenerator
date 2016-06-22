<?php

use yii\db\Migration;

class m160622_022456_change_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%users}}','admin', $this->smallInteger()->notNull()->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn('{{%users}}','admin');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
