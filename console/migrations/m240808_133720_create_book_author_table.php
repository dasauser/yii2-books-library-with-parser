<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book_author}}`.
 */
class m240808_133720_create_book_author_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book_author}}', [
            'book_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('pk_book_author', '{{%book_author}}', ['book_id', 'author_id']);

        $this->addForeignKey(
            'fk_book_author_book_id',
            '{{%book_author}}',
            'book_id',
            '{{%books}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_book_author_author_id',
            '{{%book_author}}',
            'author_id',
            '{{%authors}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%book_author}}');
    }
}
