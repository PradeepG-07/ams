<?php

class HelloCommand extends CConsoleCommand
{
    public function run($args)
    {
        echo "Hello from Yii Console Command!\n";

        if (!empty($args)) {
            echo "Arguments: " . implode(', ', $args) . "\n";
        }
    }
}
