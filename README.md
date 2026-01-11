# SIMON Craft CMS Plugin

Craft CMS plugin for integrating with the SIMON monitoring system.

## Installation

### Via Composer (Required)

Craft CMS plugins **must** be installed via Composer. Craft will not discover plugins that are manually copied to the plugins directory.

#### For Development (Path Repository)

If developing the plugin locally:

```bash
# Add the plugin as a path repository
composer config repositories.simon path /path/to/simon-craft

# Install the plugin
composer require simon/integration:@dev

# Install in Craft CMS
./craft install/plugin simon
```

#### For Production (Published Package)

If the plugin is published to Packagist or a private repository:

```bash
composer require simon/integration
./craft install/plugin simon
```

### Important Notes

- **DO NOT** manually copy the plugin to `craft/plugins/simon` - Craft won't find it
- Plugins must be installed via Composer to be discovered by Craft
- The plugin will be installed in `vendor/simon/integration/` via Composer
- After installing via Composer, use `./craft install/plugin simon` to enable it in Craft

## Configuration

### Step 1: Configure Settings

1. Go to **Settings → Plugins → SIMON**
2. Configure:
   - **API URL**: Base URL of your SIMON API (e.g., `http://localhost:3000`)
   - **Auth Key**: Your SIMON authentication key
   - **Client ID**: Your SIMON client ID
   - **Site ID**: Your SIMON site ID
   - **Enable Cron**: Enable automatic submission
3. Click **Save**

## CLI Command

Submit site data manually:

```bash
./craft simon/submit
```

## Scheduled Tasks

If enabled, the plugin automatically submits data based on Craft's task scheduler.

## What Data is Collected

- **Core**: Craft CMS version
- **Log Summary**: Error/warning counts from Craft logs
- **Environment**: PHP version, database info, web server
- **Plugins**: All installed plugins with versions

## Requirements

- Craft CMS 4.0 or higher
- PHP 8.0 or higher
- cURL extension enabled

## Troubleshooting

- Check Craft logs: `storage/logs/`
- Verify API URL is accessible
- Ensure Client ID and Site ID are configured
- Test with CLI: `./craft simon/submit`
