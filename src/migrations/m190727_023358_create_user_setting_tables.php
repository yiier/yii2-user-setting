<?php

use yii\db\Migration;

/**
 * Class m190727_023358_create_user_setting_tables
 */
class m190727_023358_create_user_setting_tables extends Migration
{
    public $tableName = '{{%user_setting}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // MySql table options
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'type' => $this->string(20)->notNull(), // group、text、select、password、
            'user_id' => $this->integer()->notNull()->defaultValue(0),
            'key' => $this->string(60)->notNull(),
            'value' => $this->text(),
            'description' => $this->string(),
            'status' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $tableOptions);
        // Indexes
        $this->createIndex('fk_user_code', $this->tableName, ['user_id', 'key'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
        return true;
    }
}
