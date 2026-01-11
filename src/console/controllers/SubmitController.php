<?php

namespace simon\console\controllers;

use Craft;
use craft\console\Controller;
use simon\Plugin;
use yii\console\ExitCode;

class SubmitController extends Controller
{
    public function actionIndex()
    {
        $this->stdout("Collecting site data...\n");

        $settings = Plugin::$plugin->getSettings();
        $apiClient = Plugin::$plugin->apiClient;
        $dataCollector = Plugin::$plugin->dataCollector;

        if (empty($settings->apiUrl) || empty($settings->authKey)) {
            $this->stderr("API URL or Auth Key not configured\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if (empty($settings->clientId) || empty($settings->siteId)) {
            $this->stderr("Client ID or Site ID not configured\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $apiClient->setConfig($settings->apiUrl, $settings->authKey);

        $siteData = $dataCollector->collect();
        $baseUrl = Craft::$app->getSites()->getCurrentSite()->getBaseUrl();

        $payload = [
            'client_id' => (int) $settings->clientId,
            'site_id' => (int) $settings->siteId,
            'cms_type' => 'craft',
            'site_name' => Craft::$app->getSites()->getCurrentSite()->name,
            'site_url' => $baseUrl,
            'data' => $siteData,
        ];

        $this->stdout("Submitting to SIMON API...\n");

        if ($apiClient->submit('intake', $payload)) {
            $this->stdout("Data submitted successfully!\n");
            return ExitCode::OK;
        }

        $this->stderr("Failed to submit data\n");
        return ExitCode::UNSPECIFIED_ERROR;
    }
}
