<?php
/**
 * SIMON Integration plugin for Craft CMS
 *
 * @copyright Copyright (c) 2024 SIMON Team
 * @link      https://simon.example.com
 * @package   Simon
 * @since     1.0.0
 */

namespace simon;

use Craft;
use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use craft\services\Plugins;
use craft\events\PluginEvent;
use simon\console\controllers\SubmitController;
use simon\services\DataCollector;
use simon\services\ApiClient;
use yii\base\Event;

/**
 * Class Simon
 *
 * @property DataCollector $dataCollector
 * @property ApiClient $apiClient
 */
class Plugin extends \craft\base\Plugin
{
    public static $plugin;

    public string $schemaVersion = '1.0.0';

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerMap = [
                'submit' => SubmitController::class,
            ];
        }

        // Register services
        $this->setComponents([
            'dataCollector' => DataCollector::class,
            'apiClient' => ApiClient::class,
        ]);

        // Register scheduled task
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    $this->scheduleTask();
                }
            }
        );
    }

    protected function createSettingsModel(): ?\craft\base\Model
    {
        return new \simon\models\Settings();
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate(
            'simon/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }

    private function scheduleTask(): void
    {
        $settings = $this->getSettings();
        if (!$settings->enableCron) {
            return;
        }

        // Add scheduled task
        Craft::$app->queue->push(new \simon\queue\jobs\SubmitDataJob());
    }
}
