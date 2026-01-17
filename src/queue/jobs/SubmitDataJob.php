<?php

namespace simon\queue\jobs;

use Craft;
use craft\queue\BaseJob;
use simon\Plugin;

class SubmitDataJob extends BaseJob
{
    public function execute($queue): void
    {
        $settings = Plugin::$plugin->getSettings();
        
        if (empty($settings->apiUrl) || empty($settings->authKey)) {
            Craft::warning('SIMON: API URL or Auth Key not configured', __METHOD__);
            return;
        }

        if (empty($settings->clientId) || empty($settings->siteId)) {
            Craft::warning('SIMON: Client ID or Site ID not configured', __METHOD__);
            return;
        }

        $dataCollector = Plugin::$plugin->dataCollector;
        $apiClient = Plugin::$plugin->apiClient;
        
        $apiClient->setConfig($settings->apiUrl, $settings->authKey);
        
        $siteData = $dataCollector->collect();
        $baseUrl = Craft::$app->getSites()->getCurrentSite()->getBaseUrl();
        $siteName = Craft::$app->getSites()->getCurrentSite()->name;

        $payload = array_merge([
            'client_id' => (int) $settings->clientId,
            'site_id' => (int) $settings->siteId,
            'auth_key' => $settings->authKey,
            'application_type' => 'craft',
            'site' => [
                'name' => $siteName,
                'url' => $baseUrl,
                'application_type' => 'craft',
            ],
        ], $siteData);

        $apiClient->submit('intake', $payload);
    }

    protected function defaultDescription(): ?string
    {
        return Craft::t('simon', 'Submit data to SIMON');
    }
}
