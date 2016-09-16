<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @param null|string $hash
     * @return string
     */
    public function actionLogin($hash=null)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($hash) {
            $model->setScenario(LoginForm::SCENARIO_LOGIN);
            $model->hash = $hash;
            if ($model->login()) {
                Yii::$app->session->addFlash('success', $model->getIsNewUser() ? 'Вы успешно зарегистрировались.' : 'Вы успешно авторизировались.');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->addFlash('error', 'Произошла ошибка.');
                return $this->redirect('login');
            }
        } else {
            if ($model->load(Yii::$app->request->post()) && $model->send()) {
                Yii::$app->session->addFlash('success', 'Проверьте вашу электронную почту для получения дальнейших инструкций.');
                return $this->refresh();
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
