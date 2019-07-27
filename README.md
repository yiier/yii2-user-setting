User Settings For Yii2
======================
User Settings For Yii2

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yiier/yii2-user-setting "*"
```

or add

```
"yiier/yii2-user-setting": "*"
```

to the require section of your `composer.json` file.


Configuration
------

### Database Migrations

Before usage this extension, we'll also need to prepare the database.


```
php yii migrate --migrationPath=@vendor/yiier/userSettings/src/migrations/
```



### Module Setup

To access the module, you need to configure the modules array in your application configuration:

```php
'modules' => [
    'userSetting' => [
        'class' => 'yiier\userSetting\Module',
    ],
],

```


Component Setup

To use the Setting Component, you need to configure the components array in your application configuration:

```php
'components' => [
    'setting' => [
        'class' => 'yiier\userSetting\UserSetting',
    ],
],
```

Usage
-----

```php
<?php
$setting = Yii::$app->setting;

$value = $setting->get('key');
$value = $setting->get('key', Yii::$app->user->id);

$setting->set('key', 125.5);
$setting->set('key', 125.5, Yii::$app->user->id);

$setting->set('key', false, Yii::$app->user->id, 'Not allowed Update Post');
$setting->set('key', false, 0, 'Not allowed Update Post');

// Checking existence of setting
$setting->has('key');
$setting->has('key', Yii::$app->user->id);

// Activates a setting
$setting->activate('key');
$setting->activate('key', Yii::$app->user->id);

// Deactivates a setting
$setting->deactivate('key');
$setting->deactivate('key', Yii::$app->user->id);

// Removes a setting
$setting->remove('key');
$setting->remove('key', Yii::$app->user->id);

// Removes all settings
$setting->removeAll();
$setting->removeAll(Yii::$app->user->id);

// Get's all values in the specific section.
$setting->getAllByUserId(Yii::$app->user->id);

$setting->invalidateCache(); // automatically called on set(), remove();
```


Reference
-----

- [yii2mod/yii2-settings](https://github.com/yii2mod/yii2-settings)