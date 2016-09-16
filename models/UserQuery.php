<?php
/**
 * Created by PhpStorm.
 * User: nekulin
 * Date: 16.09.16
 * Time: 8:02
 */

namespace app\models;


class UserQuery extends \yii\db\ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['status' => User::STATUS_ACTIVE]);
    }

    /**
     * @param string $email
     * @return $this
     */
    public function email($email)
    {
        return $this->andWhere(['email' => $email]);
    }

    /**
     * @param null $db
     * @return array|null|User
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param null $db
     * @return array|null|User[]
     */
    public function all($db = null)
    {
        return parent::all($db);
    }
}