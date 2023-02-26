<?php

namespace api\controllers\v1;

use api\models\BookSearch;
use common\models\Author;
use common\models\Book;
use common\models\BookGenre;
use common\models\Genre;
use Dersonsena\JWTTools\JWTSignatureBehavior;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;

class BookController extends \yii\rest\Controller
{
    public $modelClass = '';

    public function actions(): array
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['jwtValidator'] = [
            'class' => JWTSignatureBehavior::class,
            'secretKey' => Yii::$app->params['jwt']['secret'],
        ];

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        $behaviors['verbs'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'search' => ['GET'],
                'create' => ['POST'],
                'update' => ['PATCH'],
                'delete' => ['DELETE'],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex(): array
    {
        if($this->getIsUserAdmin()) {
            $books = Book::find()->all();
        } else {
            $books = Book::find()->where(['status' => Book::STATUS_ACTIVE])->all();
        }

        if ($books) {
            $booksData = ArrayHelper::toArray($books, Book::apiArray());
            $dataProvider = new ArrayDataProvider([
                'allModels' => $booksData,
                'pagination' => [
                    'pageSize' => 10,
                ],
                'sort' => [
                    'attributes' => ['id', 'title'],
                ],
            ]);

            return [
                'status' => 'ok',
                'data' => $dataProvider
            ];
        }

        return [
            'status' => 'error',
            'data' => 'No books found'
        ];
    }

    public function actionSearch(): array {
        $searchParams = Yii::$app->request->get();
        $searchModel = new BookSearch();
        $results = $searchModel->search($searchParams);

        if ($results) {
            $booksData = ArrayHelper::toArray($results, Book::apiArray());

            $dataProvider = new ArrayDataProvider([
                'allModels' => $booksData,
                'pagination' => [
                    'pageSize' => 10,
                ],
                'sort' => [
                    'attributes' => ['id', 'title'],
                ],
            ]);

            return [
                'status' => 'ok',
                'data' => $dataProvider
            ];
        }

        return [
            'status' => 'error',
            'data' => 'No books found'
        ];
    }

    public function actionCreate(): array
    {
        if($this->getIsUserAdmin() && $requestBody = Yii::$app->request->post()) {
            $genreTitles = explode(',', $requestBody['genres']);
            $authorId = $requestBody['author'];

            $model = new Book();
            $load = $model->load($requestBody, '');
            $model->status = Book::STATUS_ACTIVE;

            if($load && $model->validate() && $authorId) {
                $authorModel = Author::find()
                    ->andWhere(['id' => $authorId])
                    ->one();

                if(!$authorModel) {
                    return [
                        'status' => 'error',
                        'message' => 'Please specify existing author ID'
                    ];
                }

                $model->author_id = $authorModel->id;
                $model->save();

                $this->fillGenres($model->id, $genreTitles);

                return [
                    'status' => 'ok',
                ];
            }
        }

        return [
            'status' => 'error',
        ];
    }

    public function actionUpdate(int $id): array
    {
        $requestBody = Yii::$app->request->post();
        $genreTitles = explode(',', $requestBody['genres']);

        if ($this->getIsUserAdmin() && $id && !empty($requestBody)) {
            $model = Book::find()
                ->where(['id' => $id])
                ->one();

            if ($model) {
                $load = $model->load($requestBody, '');

                if($requestBody['genres']) {
                    BookGenre::deleteAll(['book_id' => $model->id]);
                    $this->fillGenres($model->id, $genreTitles);
                }

                if ($load && $model->validate() && $model->save()) {
                    return [
                        'status' => 'ok',
                    ];
                }
            }
        }
        return [
            'status' => 'error',
        ];
    }

    public function actionDelete(int $id): array
    {
        if ($this->getIsUserAdmin() && $id) {
            $model = Book::find()
                ->where(['id' => $id])
                ->one();

            if ($model) {
                $model->status = Book::STATUS_DELETED;
                if ($model->save()) {
                    return [
                        'status' => 'ok',
                    ];
                }
            }
        }
        return [
            'status' => 'error',
        ];
    }

    private function getIsUserAdmin(): bool {
        return Yii::$app->user->can('admin');
    }

    private function fillGenres(int $bookId, array $genres): void
    {
        foreach ($genres as $genre) {
            $genreModel = Genre::find()
                ->andWhere(['title' => ucfirst($genre)])
                ->one();

            if(!$genreModel) {
                $genreModel = new Genre();
                $genreModel->title = ucfirst($genre);
                $genreModel->status = Genre::STATUS_ACTIVE;
                $genreModel->save();
            }

            $bookGenre = new BookGenre();
            $bookGenre->book_id = $bookId;
            $bookGenre->genre_id = $genreModel->id;
            $bookGenre->save();
        }
    }
}