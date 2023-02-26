<?php

namespace api\controllers\v1;

use common\models\Genre;
use Dersonsena\JWTTools\JWTSignatureBehavior;
use kaabar\jwt\JwtHttpBearerAuth;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;

class GenreController extends \yii\rest\Controller
{
    public $modelClass = '';

    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['jwtValidator'] = [
            'class' => JWTSignatureBehavior::class,
            'secretKey' => Yii::$app->params['jwt']['secret'],
        ];

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        if ($this->getIsUserAdmin()) {
            $genres = Genre::find()->all();
        } else {
            $genres = Genre::find()->where(['status' => Genre::STATUS_ACTIVE])->all();
        }

        if ($genres) {
            $genresData = ArrayHelper::toArray($genres, Genre::apiArray());
            $dataProvider = new ArrayDataProvider([
                'allModels' => $genresData,
                'pagination' => [
                    'pageSize' => 10,
                ],
                'sort' => [
                    'attributes' => ['id', 'name'],
                ],
            ]);

            return [
                'status' => 'ok',
                'data' => $dataProvider
            ];
        }

        return [
            'status' => 'ok',
            'data' => 'No genres found'
        ];
    }

    public function actionCreate() {
        if($this->getIsUserAdmin()) {
            $model = new Genre();
            $load = $model->load(Yii::$app->request->post(), '');

            if($load && $model->validate() && $model->save()) {
                return [
                    'status' => 'ok',
                ];
            }
        }

        return [
            'status' => 'error',
        ];
    }

    public function actionUpdate($id)
    {
        $requestBody = Yii::$app->request->post();

        if ($this->getIsUserAdmin() && $id && !empty($requestBody)) {
            $model = Genre::find()
                ->where(['id' => $id])
                ->one();

            if ($model) {
                if ($model->load($requestBody, '') && $model->validate() && $model->save()) {
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

    public function actionDelete($id)
    {
        if ($this->getIsUserAdmin() && $id) {
            $model = Genre::find()
                ->where(['id' => $id])
                ->one();

            if ($model) {
                $model->status = Genre::STATUS_DELETED;
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

    private function getIsUserAdmin() {
        return Yii::$app->user->can('admin');
    }
}