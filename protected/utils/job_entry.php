<?php
 
require 'pre_job_entry.php';
 
$app = Yii::createConsoleApplication($config);
 
// if (!in_array('--skipRedis', $_SERVER['argv'])) {
//     Yii::app()->cache->executeCommand('client', [
//         'setname',
//         'main-app-console-' . implode('-', array_slice($_SERVER['argv'], 1)),
//     ]);
// }
 
$app->run();