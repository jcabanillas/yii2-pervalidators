# Yii2 Peru Data Validators (yii2-pervalidators)

Validators for two data strings used in Peru for identity, RUC, for use with the [Yii 2.* framework][1].

## Installation

Add dependency to your ```composer.json``` file:

```json
{
    "require": {
        "jcabanillas/yii2-rucvalidators": "0.0.1"
    }
}
```

## Usage

In order to use the validator, you need to provide the full path to the validator in the model:

```php
public function rules()
{
    return [
       [['rfc'], RfcValidator::className()],
       [['curp'], CurpValidator::className()],
    ];
}
```

[1]: https://github.com/yiisoft/yii2 "Yii Framework"