<?php
namespace console\controllers;

use Yii;
use common\models\User;

class RbacController extends \yii\console\Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->getAuthManager();
        $auth->removeAll();

        $admin = $auth->createRole(User::ROLE_ADMIN);
        $client = $auth->createRole(User::ROLE_CLIENT);

        $auth->add($admin);
        $auth->add($client);

        $adminUser = User::find()->andWhere(['email' => 'admin@test.com'])->one();
        $clientUser = User::find()->andWhere(['email' => 'client@test.com'])->one();

        $auth->assign($admin, $adminUser->id);
        $auth->assign($client, $clientUser->id);
    }

    public function actionTest()
    {

    }
}