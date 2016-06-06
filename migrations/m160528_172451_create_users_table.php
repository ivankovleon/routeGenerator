<?php

use yii\db\Migration;

class m160528_172451_create_users_table extends Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'username' => $this->string()->notNull(),
            'auth_key' => $this->string(32),
            'email_confirm_token' => $this->string(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string(),
            'email' => $this->string()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('idx_user_username', '{{%users}}', 'username');
        $this->createIndex('idx_user_email', '{{%users}}', 'email');
        $this->createIndex('idx_user_status', '{{%users}}', 'status');
    }

    public function down()
    {
        $this->dropTable('{{%users}}');
    }
}
