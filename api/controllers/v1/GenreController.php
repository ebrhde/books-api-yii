<?php

namespace api\controllers\v1;

use common\models\Genre;
use Dersonsena\JWTTools\JWTSignatureBehavior;
use kaabar\jwt\JwtHttpBearerAuth;
use Yii;
use yii\filters\auth\HttpBearerAuth;

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
            'except' => ['login'] // it's doesn't run in login action
        ];

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
//            'except' => ['login'] // it's doesn't run in login action
        ];

        return $behaviors;
    }

    public function actionCreate() {

        if(Yii::$app->user->can('admin')) {
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
}