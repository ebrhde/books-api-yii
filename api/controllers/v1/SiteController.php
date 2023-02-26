<?php

namespace api\controllers\v1;

use common\models\LoginForm;
use common\models\User;
use DateTimeImmutable;
use Dersonsena\JWTTools\JWTSignatureBehavior;
use Dersonsena\JWTTools\JWTTools;
use Yii;
use yii\filters\auth\HttpBearerAuth;

class SiteController extends \yii\rest\Controller
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
            'except' => ['login'] // it's doesn't run in login action
        ];

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['login'] // it's doesn't run in login action
        ];

        $behaviors['verbs'] = [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'login' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @return array|LoginForm
     */
    public function actionLogin() {
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post(), '') && $model->login()) {
            $user = Yii::$app->user->identity;

            $token = JWTTools::build(Yii::$app->params['jwt']['secret'])
                ->withModel($user, ['username', 'email'])
                ->getJWT();

            return ['success' => true, 'token' => $token];
        }
        else
        {
            $model->validate();
            return $model;
        }
    }
}