<?php

namespace api\models;

use common\models\Book;
use yii\data\ActiveDataProvider;

class BookSearch extends \common\models\Book
{
    public ?string $author = null;
    public ?string $country = null;
    public ?string $genre = null;
    public ?string $date = null;

    public function rules()
    {
        return [
            [['genre', 'author', 'country', 'date'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Book::find()
            ->distinct()
            ->alias('b')
            ->andWhere(['b.status' => Book::STATUS_ACTIVE]);

        $this->load($params, '');

        if (!$this->validate()) {
            return null;
        }

        if ($this->author) {
            $query->joinWith(['author a'])
                ->andWhere(['like', 'a.name', '%' . ucfirst($this->author) . '%', false]);
        }

        if ($this->country) {
            $query->joinWith(['author a'])
                ->andWhere(['a.country' => ucfirst($this->country)]);
        }

        if ($this->genre) {
            $query->joinWith(['genres g'])
                ->andWhere(['g.title' => ucfirst($this->genre)]);
        }

        if ($this->date) {
            $query->andWhere(['b.publishing_date' => $this->date]);
        }

        $query->orderBy(['b.id' => SORT_ASC]);

        return $query->all();
    }

    public function formName()
    {
        return '';
    }
}