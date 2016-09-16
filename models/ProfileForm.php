<?php
namespace app\models;

use Yii;
use yii\base\Model;

class ProfileForm extends Model
{
    public $username;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'trim'],
            [['username'], 'string', 'max' => 255, 'min' => 3],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'username' =>  'Имя',
        ];
    }

    public function save(User $user)
    {
        if (!$this->validate()) {
            return false;
        }
        $user->username = $this->username;
        return $user->save(false, ['username']);
    }

    public function loadUser(User $user)
    {
        $this->username = $user->username;
    }
}