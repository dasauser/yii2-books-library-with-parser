<?php

namespace console\controllers;

use common\models\User;
use frontend\models\SignupForm;
use frontend\models\VerifyEmailForm;
use yii\console\Controller;
use yii\console\ExitCode;

class SeedController extends Controller
{
    public function actionIndex()
    {

        return $this->signupAdmin();
    }

    /**
     * @return void
     */
    public function signupAdmin(): int
    {
        if (User::findOne(['email' => 'admin@example.com'])) {
            echo "User already exists.\n";
            return ExitCode::OK;
        }

        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $signupForm = new SignupForm([
                'username' => 'admin',
                'password' => 'admin',
                'email' => 'admin@example.com',
            ]);

            $signupForm->signup();
            $user = User::findOne(User::find()->max('id'));

            $verifyEmailForm = new VerifyEmailForm($user->verification_token);
            $verifyEmailForm->verifyEmail();
            $transaction->commit();
        } catch (\Exception $exception) {
            $transaction->rollBack();
            throw $exception;
        }

        echo "User successfully created.\n";
        return ExitCode::OK;
    }
}