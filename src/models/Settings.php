<?php

namespace simon\models;

use craft\base\Model;

class Settings extends Model
{
    public ?string $apiUrl = null;
    public ?string $authKey = null;
    public ?string $clientId = null;
    public ?string $siteId = null;
    public bool $enableCron = false;

    public function rules(): array
    {
        return [
            [['apiUrl', 'authKey'], 'required', 'when' => function($model) {
                return !empty($model->clientId) || !empty($model->siteId);
            }],
            [['apiUrl'], 'url'],
            [['enableCron'], 'boolean'],
            [['clientId', 'siteId'], 'string'],
        ];
    }
}
