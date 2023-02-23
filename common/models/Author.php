<?php

namespace common\models;

use Yii;
use yii\db\Expression;

/**
* This is the model class for table "author".
 *
 * @property int $id
* @property string $created_at
* @property string $updated_at
* @property int|null $status
* @property string|null $name
* @property string|null $country
*
*/

class Author extends \yii\db\ActiveRecord
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
        return '{{%author}}';
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
            [['name', 'country'], 'string', 'max' => 255],
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
            'title' => 'Name',
            'country' => 'Country',
        ];
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

    /**
     * Gets query for books
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Book::className(), ['id' => 'author_id']);
    }
}