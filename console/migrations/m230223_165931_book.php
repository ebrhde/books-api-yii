<?php

use yii\db\Migration;

/**
 * Class m230223_165931_book
 */
class m230223_165931_book extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE NOW()'),
            'status' => $this->tinyInteger(1),
            'title' => $this->string(255),
            'author_id' => $this->integer(11),
            'publishing_date' => $this->dateTime()
        ], $tableOptions);

        $this->createTable('{{%book_genre}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'book_id' => $this->integer(11),
            'genre_id' => $this->integer(11)
        ], $tableOptions);

        $this->addForeignKey(
            'FK_book_author',
            '{{%book}}',
            'author_id',
            '{{%author}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK_bg_book',
            '{{%book_genre}}',
            'book_id',
            '{{%book}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK_bg_genre',
            '{{%book_genre}}',
            'genre_id',
            '{{%genre}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%genre}}');
    }
}
