<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2019-07-27 11:08
 * description:
 */

namespace yiier\userSetting\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%user_setting}}".
 *
 * @property int $id
 * @property string $type
 * @property int $user_id
 * @property string $key
 * @property string $value
 * @property string $description
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class UserSettingModel extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_setting}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'key'], 'required'],
            [['user_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['value'], 'string'],
            [['type'], 'string', 'max' => 20],
            [['key'], 'string', 'max' => 60],
            [['description'], 'string', 'max' => 255],
            [['user_id', 'key'], 'unique', 'targetAttribute' => ['user_id', 'key']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'user_id' => Yii::t('app', 'User ID'),
            'key' => Yii::t('app', 'Key'),
            'value' => Yii::t('app', 'Value'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }


    /**
     * Creates an [[ActiveQueryInterface]] instance for query purpose.
     *
     * @return TargetSettingQuery
     */
    public static function find(): TargetSettingQuery
    {
        return new TargetSettingQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();
        Yii::$app->userSetting->invalidateCache();
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->userSetting->invalidateCache();
    }

    /**
     * Return array of settings
     *
     * @return array
     */
    public function getSettings(): array
    {
        $result = [];
        $settings = static::find()->active()->asArray()->all();
        foreach ($settings as $setting) {
            $userId = $setting['user_id'];
            $key = $setting['key'];
            $settingOptions = [
                'type' => $setting['type'],
                'value' => $setting['value'],
                'description' => $setting['description']
            ];
            if (isset($result[$userId][$key])) {
                ArrayHelper::merge($result[$userId][$key], $settingOptions);
            } else {
                $result[$userId][$key] = $settingOptions;
            }
        }
        return $result;
    }

    /**
     * Set setting
     *
     * @param $key
     * @param $value
     * @param int $userId
     * @param string $description
     * @return bool
     */
    public function setSetting($key, $value, $userId = 0, $description = ''): bool
    {
        if (!$model = static::find()->where(['user_id' => $userId, 'key' => $key])->limit(1)->one()) {
            $model = new static();
        }
        $model->user_id = $userId;
        $model->key = $key;
        $model->value = strval($value);
        $model->description = strval($description);
        $model->type = gettype($value);
        return $model->save();
    }

    /**
     * Remove setting
     *
     * @param $key
     * @param integer $userId
     *
     * @return bool|int|null
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function removeSetting($key, $userId = 0)
    {
        if (!$model = static::find()->where(['user_id' => $userId, 'key' => $key])->limit(1)->one()) {
            return $model->delete();
        }
        return false;
    }

    /**
     * Remove all settings
     *
     * @param int $userId
     * @return int
     */
    public function removeAllSettings($userId = 0): int
    {
        return static::deleteAll(['user_id' => $userId]);
    }

    /**
     * Activates a setting
     *
     * @param $key
     *
     * @param int $userId
     * @return bool
     */
    public function activateSetting($key, $userId = 0): bool
    {
        $model = static::find()->where(['user_id' => $userId, 'key' => $key])->limit(1)->one();
        if ($model && $model->status === self::STATUS_INACTIVE) {
            $model->status = self::STATUS_ACTIVE;
            return $model->save(true, ['status']);
        }
        return false;
    }

    /**
     * Deactivates a setting
     *
     * @param $key
     *
     * @param int $userId
     * @return bool
     */
    public function deactivateSetting($key, $userId = 0): bool
    {
        $model = static::find()->where(['user_id' => $userId, 'key' => $key])->limit(1)->one();
        if ($model && $model->status === self::STATUS_ACTIVE) {
            $model->status = self::STATUS_INACTIVE;
            return $model->save(true, ['status']);
        }
        return false;
    }
}
