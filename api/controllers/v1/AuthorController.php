<?php

namespace api\controllers\v1;

use common\models\Author;
use Dersonsena\JWTTools\JWTSignatureBehavior;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

class AuthorController extends Controller
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

        if($this->getIsUserAdmin()) {
            $authors = Author::find()->all();

        } else {
            $authors = Author::find()->where(['status' => Author::STATUS_ACTIVE])->all();
        }

        if ($authors) {
            $authorsData = ArrayHelper::toArray($authors, Author::apiArray());
            $dataProvider = new ArrayDataProvider([
                'allModels' => $authorsData,
                'pagination' => [
                    'pageSize' => 10,
                ],
                'sort' => [
                    'attributes' => ['name'],
                ],
            ]);

            return [
                'status' => 'ok',
                'data' => $dataProvider
            ];
        }
        return [
            'status' => 'error',
            'data' => 'No authors found'
        ];
    }

    public function actionCreate() {
        if($this->getIsUserAdmin()) {
            $model = new Author();
            $load = $model->load(Yii::$app->request->post(), '');
            $model->status = Author::STATUS_ACTIVE;

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

        if ($this->getIsUserAdmin() && !empty($requestBody)) {
            $model = Author::find()
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
            $model = Author::find()
                ->where(['id' => $id])
                ->one();

            if ($model) {
                $model->status = Author::STATUS_DELETED;
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