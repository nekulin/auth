<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\Url;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    const SCENARIO_LOGIN = 'login';
    const HASH_PREFIX = 'auth_';
    const HASH_DURATION = 3600;

    public $email;
    public $hash;
    public $rememberMe = true;

    private $_user = false;
    private $_isNewUser;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['hash'], 'required', 'on' => self::SCENARIO_LOGIN],
            [['hash'], function(){
                $cache = Yii::$app->cache->get('auth_' . $this->hash);
                if (!$cache) {
                    $this->addError('hash', 'Неверная ссылка.');
                }
                $this->email = $cache['email'];
            }, 'on' => self::SCENARIO_LOGIN],
            [['hash'], 'required', 'on' => self::SCENARIO_LOGIN],

            [['email'], 'required'],
            [['email'], 'email'],
            ['rememberMe', 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'E-mail',
            'rememberMe' => 'Запомнить',
        ];
    }

    /**
     * Send email
     * @return bool
     */
    public function send()
    {
        $hash = uniqid();
        Yii::$app->cache->set(self::HASH_PREFIX . $hash, [
            'email' => $this->email,
            'time' => time()
        ], self::HASH_DURATION);

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'login-html'],
                [
                    'url' => Url::to(['/site/login', 'hash' => $hash], true)
                ]
            )
            ->setFrom([Yii::$app->params['noreplyEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Вход на сайт ' . Yii::$app->name)
            ->send();
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $this->_isNewUser = false;
            // add user
            $user = User::find()->active()->email($this->email)->one();
            if (!$user) {
                $user = new User();
                $user->email = $this->email;
                if (!$user->save()) {
                    $this->addError('email', 'Произошла ошибка.');
                    return false;
                }
                $this->_isNewUser = true;
            }
            $this->_user = $user;
            Yii::$app->cache->delete(self::HASH_PREFIX . $this->hash);
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->email);
        }

        return $this->_user;
    }

    public function getIsNewUser()
    {
        return $this->_isNewUser;
    }
}
