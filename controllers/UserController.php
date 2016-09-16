<?php

namespace app\controllers;

use app\models\ProfileForm;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;;

class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * Profile
     *
     * @return string
     */
    public function actionProfile()
    {
        /** @var User $user */
        $user = Yii::$app->user->getIdentity();

        $model = new ProfileForm();
        $model->loadUser($user);
        if ($model->load(Yii::$app->request->post()) && $model->save($user)) {
            Yii::$app->session->addFlash('success', 'Личная информация успешно сохранена.');
        }

        return $this->render('profile', [
            'model' => $model,
            'user' => $user,
        ]);
    }
}
