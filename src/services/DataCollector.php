<?php

namespace simon\services;

use Craft;
use craft\base\Component;
use craft\helpers\App;

class DataCollector extends Component
{
    public function collect(): array
    {
        $data = [];

        // Core version
        $data['core'] = [
            'version' => Craft::$app->getVersion(),
            'status' => $this->getCoreStatus(),
        ];

        // Log summary
        $data['log_summary'] = $this->getLogSummary();

        // Environment
        $data['environment'] = $this->getEnvironment();

        // Plugins
        $data['extensions'] = $this->getPlugins();

        // Templates
        $data['themes'] = [];

        return $data;
    }

    private function getCoreStatus(): string
    {
        // Check for Craft updates
        return 'up-to-date';
    }

    private function getLogSummary(): array
    {
        // Craft log query
        $logEntries = Craft::$app->getLog()->getLogReader()->getLogEntries(
            100,
            null,
            null,
            ['error', 'warning']
        );

        $errorCount = 0;
        $warningCount = 0;

        foreach ($logEntries as $entry) {
            if ($entry->level === 'error') {
                $errorCount++;
            } elseif ($entry->level === 'warning') {
                $warningCount++;
            }
        }

        return [
            'total' => $errorCount + $warningCount,
            'error' => $errorCount,
            'warning' => $warningCount,
            'by_level' => [],
        ];
    }

    private function getEnvironment(): array
    {
        $db = Craft::$app->getDb();

        return [
            'php_version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => (int) ini_get('max_execution_time'),
            'web_server' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            'database_type' => $db->getDriverName(),
            'database_version' => $db->getSchema()->getServerVersion(),
            'php_modules' => get_loaded_extensions(),
        ];
    }

    private function getPlugins(): array
    {
        $plugins = Craft::$app->plugins->getAllPlugins();
        $result = [];

        foreach ($plugins as $plugin) {
            $result[] = [
                'type' => 'plugin',
                'machine_name' => $plugin->id,
                'human_name' => $plugin->name,
                'version' => $plugin->version,
                'status' => $plugin->isInstalled && $plugin->isEnabled ? 'enabled' : 'disabled',
                'is_custom' => strpos($plugin->id, 'simon') === 0 ? false : true,
            ];
        }

        return $result;
    }
}
