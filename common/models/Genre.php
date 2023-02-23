<?php

namespace common\models;

use yii\db\Expression;

/**
* This is the model class for table "genre".
 *
 * @property int $id
* @property string $created_at
* @property string $updated_at
* @property int|null $status
* @property string|null $title
*
*/

class Genre extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 9;

    private static $_statuses = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_DELETED => 'Deleted',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%genre}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status ID',
            'title' => 'Title',
        ];
    }

    /**
     * Gets query for [[GenreBooks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGenreBooks()
    {
        return $this->hasMany(BookGenre::className(), ['genre_id' => 'id']);
    }

    public function getBooks()
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])
            ->via('genreBooks');
    }


    public static function getStatuses()
    {
        return self::$_statuses;
    }

    public function getStatus($id = 0)
    {
        if (!$id) $id = $this->status;
        return ((!empty(self::$_statuses[$id])) ? self::$_statuses[$id] : 'No Specify');
    }
}