<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2019-07-27 11:21
 * description:
 */

namespace yiier\userSetting\models;


use yii\db\ActiveQuery;

class UserSettingQuery extends ActiveQuery
{
    /**
     * Scope for settings with active status
     *
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['status' => UserSettingModel::STATUS_ACTIVE]);
    }

    /**
     * Scope for settings with inactive status
     *
     * @return $this
     */
    public function inactive()
    {
        return $this->andWhere(['status' => UserSettingModel::STATUS_INACTIVE]);
    }
}
