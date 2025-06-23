<?php

// Test configuration - inherits from main config but overrides for testing
$config = require(dirname(__FILE__) . '/main.php');

// Override components for testing
$config['components']['mongodb'] = array(
    'class' => 'EMongoDB',
    'connectionString' => 'mongodb://localhost:27017', // Test database
    'dbName' => 'test_db',
    'fsyncFlag' => false,
    'safeFlag' => false,
);

// Disable logging for tests
$config['components']['log'] = array(
    'class' => 'CLogRouter',
    'routes' => array(),
);

// Use array cache for tests (faster)
$config['components']['cache'] = array(
    'class' => 'CArrayCache',
);

return $config;
