<?php
require_once(__DIR__ . '/vendor/autoload.php');
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

function publish_mqtt($topic, $payload) {
    try {
        $server = getenv('MQTT_CLOUD_BROKER') ?: 'duck-01.lmq.cloudamqp.com';
        $port = getenv('MQTT_CLOUD_PORT') ?: 8883;
        $user = getenv('MQTT_CLOUD_USER') ?: 'xdagoqsj:xdagoqsj';
        $pass = getenv('MQTT_CLOUD_PASS') ?: 'flwvAT0Npo8piPIZehUr_PnKPrs1JJ8L';
        $clientId = 'php_mqtt_' . uniqid();

        $mqtt = new MqttClient($server, $port, $clientId);
        $settings = (new ConnectionSettings)
            ->setUsername($user)
            ->setPassword($pass)
            ->setUseTls(true)
            ->setTlsSelfSignedAllowed(true);

        $mqtt->connect($settings, true);
        $mqtt->publish($topic, json_encode($payload), 1);
        $mqtt->disconnect();
        error_log("📡 MQTT publicado en $topic: " . json_encode($payload));
    } catch (Exception $e) {
        error_log("⚠️ Error MQTT: " . $e->getMessage());
    }
}
?>
