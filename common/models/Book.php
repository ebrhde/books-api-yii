<?php

namespace common\models;

use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property int|null $status
 * @property string|null $title
 * @property int|null $author_id
 * @property string|null $publishing_date
 */

class Book extends \yii\db\ActiveRecord
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
        return '{{%book}}';
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
            [['created_at', 'updated_at', 'publishing_date'], 'safe'],
            [['status', 'author_name'], 'integer'],
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
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
            'status' => 'Status ID',
            'title' => 'Title',
            'author_id' => 'Author ID',
            'publishing_date' => 'Publishing Date',
        ];
    }

    public function loadDependencies()
    {
        $this->genresBook = ArrayHelper::getColumn($this->genres, 'id');
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

    public function getBookGenres()
    {
        return $this->hasMany(BookGenre::class, ['book_id' => 'id']);
    }

    public function getGenres()
    {
        return $this->hasMany(Genre::class, ['id' => 'genre_id'])
            ->via('bookGenres');
    }
}