<?php
 
class TestCommand extends CConsoleCommand
{
    public function run($args)
    {
        echo "Hello from Yii Console Application!\n";
        var_dump($args);
        $this->test();
    }
 
    public function test()
    {
        $record = Todo::model()->findAll();
        
        // UtilityHelpers::prettyPrint($record);
        var_dump("hi" . json_encode($record, JSON_PRETTY_PRINT));
    }
}
 
 