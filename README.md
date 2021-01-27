User Settings For Yii2
======================
User Settings For Yii2

[![Latest Stable Version](https://poser.pugx.org/yiier/yii2-user-setting/v/stable)](https://packagist.org/packages/yiier/yii2-user-setting)
[![Total Downloads](https://poser.pugx.org/yiier/yii2-user-setting/downloads)](https://packagist.org/packages/yiier/yii2-user-setting)
[![Latest Unstable Version](https://poser.pugx.org/yiier/yii2-user-setting/v/unstable)](https://packagist.org/packages/yiier/yii2-user-setting)
[![License](https://poser.pugx.org/yiier/yii2-user-setting/license)](https://packagist.org/packages/yiier/yii2-user-setting)

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
php yii migrate --migrationPath=@yiier/userSetting/migrations/
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
    'userSetting' => [
        'class' => 'yiier\userSetting\UserSetting',
    ],
],
```

Usage
-----

```php
<?php
$setting = Yii::$app->userSetting;

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

$setting->invalidateCache(Yii::$app->user->id); // automatically called on set(), remove();
```

UserSettingAction
-----

To use a custom settings form, you can use the included `UserSettingAction`.

1. Create a model class with your validation rules.
2. Create an associated view with an `ActiveForm` containing all the settings you need.
3. Add `yiier\userSetting\UserSettingAction` to the controller's actions.

The settings will be stored in section taken from the form name, with the key being the field name.

### Model:

```php
<?php
class SiteForm extends Model
{

    public $siteName, $siteDescription;

    public function rules()
    {
        return [
            [['siteName', 'siteDescription'], 'string'],
        ];
    }

    public function fields()
    {
        return ['siteName', 'siteDescription'];
    }

    public function attributes()
    {
        return ['siteName', 'siteDescription'];
    }

    public function attributeLabels()
    {
        return [
            'siteName' => 'Site Name',
            'siteDescription' => 'Site Description'
        ];
    }

}
```

### Views:

```php
<?php $form = ActiveForm::begin(['id' => 'site-settings-form']); ?>

<?= $form->field($model, 'siteName') ?>
<?= $form->field($model, 'siteDescription') ?>
<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end(); ?>

```

### Controller:

```php
public function actions() 
{
   return [
   		//....
            'site-settings' => [
                'class' => UserSettingAction::class,
                'modelClass' => 'app\models\SiteForm',
                //'scenario' => 'site',	// Change if you want to re-use the model for multiple setting form.
                //'userId' => 0', // By default use \Yii::$app->user->id
                'viewName' => 'site-settings',	// The form we need to render
                'successMessage' => '保存成功'
            ],
        //....
    ];
}
```

Reference
-----

- [yii2mod/yii2-settings](https://github.com/yii2mod/yii2-settings)
- [phemellc/yii2-settings](https://github.com/phemellc/yii2-settings)
