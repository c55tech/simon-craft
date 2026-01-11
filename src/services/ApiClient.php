<?php

namespace simon\services;

use Craft;
use craft\base\Component;
use craft\helpers\App;
use yii\base\Exception;

class ApiClient extends Component
{
    private ?string $baseUrl = null;
    private ?string $authKey = null;

    public function setConfig(string $baseUrl, string $authKey): void
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->authKey = $authKey;
    }

    public function submit(string $endpoint, array $data): bool
    {
        if (empty($this->baseUrl) || empty($this->authKey)) {
            Craft::error('SIMON API: Base URL or Auth Key not configured', __METHOD__);
            return false;
        }

        $url = $this->baseUrl . '/api/' . ltrim($endpoint, '/');

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-Auth-Key: ' . $this->authKey,
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            Craft::error('SIMON API Error: ' . $error, __METHOD__);
            return false;
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            Craft::info("SIMON API: Successfully submitted to {$endpoint}", __METHOD__);
            return true;
        } else {
            Craft::error("SIMON API: HTTP {$httpCode} error when submitting to {$endpoint}. Response: {$response}", __METHOD__);
            return false;
        }
    }
}
