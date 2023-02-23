<?php

use common\models\User;
use yii\db\Expression;
use yii\db\Migration;

/**
 * Class m230223_160658_add_test_users
 */
class m230223_160658_add_test_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%user}}', [
            'username' => 'admin',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => '$2y$10$Zr7YDYZc4Xiz20bt8T2.MeKfo0ksRPKM6RYzHwe5gVdnpT5AaWYGy',
            'email' => 'admin@test.com',
            'status' => User::STATUS_ACTIVE,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'verification_token' => Yii::$app->security->generateRandomString() . '_' . time(),
        ]);

        $this->insert('{{%user}}', [
            'username' => 'client',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => '$2y$10$QSk3sVZxCsesi4csbGXx4u.Trbu2thOFf18mSb4ebFWLrTYwpn4sa',
            'email' => 'client@test.com',
            'status' => User::STATUS_ACTIVE,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'verification_token' => Yii::$app->security->generateRandomString() . '_' . time()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%user}}', ['username' => 'admin']);
        $this->delete('{{%user}}', ['username' => 'client']);
    }
}
