#!/usr/bin/php

<?php

require __DIR__ . '/aws.phar';
require __DIR__ . '/utils.inc.php';

$APP_CONFIG_APP= 'aws-appconfig-demo';
$APP_CONFIG_ENVIRONMENT = 'prod';
$APP_CONFIG_CONFIGURATION_PROFILE = 'app';

$appConfigClient = new \Aws\AppConfig\AppConfigClient(['version' => 'latest', 'region' => getenv('AWS_REGION')]);

try {
	$configMeta = readJsonFile('/tmp/config_meta.json');
	echo("Config meta file is already present: " . json_encode($configMeta) . "\n");
	$clientId = $configMeta->ClientId;
	$version = $configMeta->ClientConfigurationVersion;
} catch (RuntimeException $e) {
	echo("No config meta file is present. Generating new Client ID and default version of 0.\n");
	$clientId = uniqid('', true);
	$version = 0;
}

$params = [
	'Application' => $APP_CONFIG_APP,
	'ClientConfigurationVersion' => $version,
	'ClientId' => $clientId,
	'Configuration' => $APP_CONFIG_CONFIGURATION_PROFILE,
	'Environment' => $APP_CONFIG_ENVIRONMENT
];

try {
	$response = $appConfigClient->getConfiguration($params);

	$config = json_decode($response['Content']->getContents());
	if ($config === null) {
		echo("No changes to configuration.\n");
	} else {
		echo("There are changes to configuration. Writing.\n");
		writeJsonFile(__DIR__ . '/config.json', $config);
	}

	// Save config metadata
	echo("Writing meta.\n");
	writeJsonFile('/tmp/config_meta.json', ['ClientConfigurationVersion' => $response['ConfigurationVersion'], 'ClientId' => $clientId]);
} catch (\Aws\AppConfig\Exception\AppConfigException $e) {
	echo("Failed to retrieve new config: {$e->getMessage()}");
}

