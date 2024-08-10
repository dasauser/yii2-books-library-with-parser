<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%books}}`.
 */
class m240808_132502_create_books_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%books}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull()->unique(),
            'isbn' => $this->string(20),
            'pageCount' => $this->smallInteger(),
            'publishedDate' => $this->dateTime(),
            'thumbnailUrl' => $this->string(),
            'thumbnailImage' => $this->string(),
            'shortDescription' => $this->text(),
            'longDescription' => $this->text(),
            'status' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%books}}');
    }
}
