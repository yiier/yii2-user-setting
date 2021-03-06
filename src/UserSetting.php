<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2019-07-27 11:08
 * description:
 */

namespace yiier\userSetting;

use Yii;
use yii\caching\Cache;
use yii\di\Instance;
use yii\helpers\ArrayHelper;

class UserSetting extends \yii\base\Component
{
    /**
     * @var string setting model class name
     */
    public $modelClass = 'yiier\userSetting\models\UserSettingModel';
    /**
     * @var Cache|array|string the cache used to improve RBAC performance. This can be one of the followings:
     *
     * - an application component ID (e.g. `cache`)
     * - a configuration array
     * - a [[yii\caching\Cache]] object
     *
     * When this is not set, it means caching is not enabled
     */
    public $cache = 'cache';
    /**
     * @var string the key used to store settings data in cache
     */
    public $cacheKey = 'yiier-user-setting';
    /**
     * @var \yiier\userSetting\models\UserSettingModel setting model
     */
    protected $model;
    /**
     * @var array list of settings
     */
    protected $items;
    /**
     * @var mixed setting value
     */
    protected $setting;

    /**
     * Initialize the component
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if ($this->cache !== null) {
            $this->cache = Instance::ensure($this->cache, Cache::class);
        }
        $this->model = Yii::createObject($this->modelClass);
    }

    /**
     * Get's all values by user id.
     *
     * @param int $userId
     * @param null $default
     *
     * @return mixed
     */
    public function getAllByUserId(int $userId, $default = null)
    {
        $items = $this->getSettingsConfig($userId);
        if (isset($items[$userId])) {
            $this->setting = ArrayHelper::getColumn($items[$userId], 'value');
        } else {
            $this->setting = $default;
        }
        return $this->setting;
    }

    /**
     * Get's the value for the key and user id.
     *
     * @param string $key
     * @param integer $userId
     * @param null $default
     *
     * @return mixed
     */
    public function get(string $key, $userId = 0, $default = null)
    {
        $items = $this->getSettingsConfig($userId);
        if (isset($items[$userId][$key])) {
            return ArrayHelper::getValue($items[$userId][$key], 'value');
        }
        return $default;
    }

    /**
     * Add a new setting or update an existing one.
     *
     * @param string $key
     * @param string $value
     * @param int $userId
     * @param string $description
     *
     * @return bool
     */
    public function set($key, $value, $userId = 0, $description = ''): bool
    {
        if ($this->model->setSetting($key, $value, $userId, $description)) {
            if ($this->invalidateCache($userId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checking existence of setting
     *
     * @param string $key
     * @param int $userId
     * @return bool
     */
    public function has($key, $userId = 0): bool
    {
        $setting = $this->get($key, $userId);
        return !empty($setting);
    }

    /**
     * Remove setting by user id and key
     *
     * @param string $key
     * @param int $userId
     * @return bool
     * @throws \Throwable
     */
    public function remove($key, $userId = 0): bool
    {
        if ($this->model->removeSetting($key, $userId)) {
            if ($this->invalidateCache($userId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Remove all settings
     *
     * @param int $userId
     * @return bool
     */
    public function removeAll(int $userId = 0): bool
    {
        if ($this->model->removeAllSettings($userId)) {
            if ($this->invalidateCache($userId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Activates a setting
     *
     * @param string $key
     * @param int $userId
     * @return bool
     */
    public function activate($key, $userId = 0): bool
    {
        return $this->model->activateSetting($key, $userId);
    }

    /**
     * Deactivates a setting
     *
     * @param string $key
     * @param int $userId
     * @return bool
     */
    public function deactivate($key, $userId = 0): bool
    {
        return $this->model->deactivateSetting($key, $userId);
    }

    /**
     * Returns the settings config
     *
     * @param int $userId
     * @return array
     */
    protected function getSettingsConfig(int $userId): array
    {
        if (!$this->cache instanceof Cache) {
            $this->items = $this->model->getSettings($userId);
        } else {
            $cacheKey = "{$this->cacheKey}:{$userId}";
            $cacheItems = $this->cache->get($cacheKey);
            if (!empty($cacheItems)) {
                $this->items = $cacheItems;
            } else {
                $this->items = $this->model->getSettings($userId);
                $this->cache->set($cacheKey, $this->items);
            }
        }
        return $this->items;
    }

    /**
     * Invalidate the cache
     *
     * @param $userId
     * @return bool
     */
    public function invalidateCache($userId): bool
    {
        if ($this->cache !== null) {
            $cacheKey = "{$this->cacheKey}:{$userId}";
            $this->cache->delete($cacheKey);
            $this->items = null;
        }
        return true;
    }
}
