<?php
 
class MongoDbLogRoute extends CLogRoute
{
    /**
     * @var string The name of the MongoDB collection for storing logs
     */
    // public $collectionName = 'YiiLog';
 
    /**
     * @var string The ID of the MongoDB connection component
     */
    // public $connectionID = 'mongodb';
 
    /**
     * @var EMongoDB The MongoDB connection instance
     */
    private $_mongo;
 
    /**
     * Initializes the log route.
     */
    // public function init()
    // {
    //     parent::init();
    //     $this->_mongo = $this->getMongoConnection();
    // }
 
    /**
     * Processes log messages and stores them in MongoDB.
     * @param array $logs
     */
    protected function processLogs($logs)
    {
        foreach ($logs as $log) {
            $entry = new MongoLog();
            $entry->level = $log[1];
            $entry->category = $log[2];
            $entry->logtime = new MongoDate((int)$log[3]);
            $entry->message = $log[0];
            $entry->save();
        }
    }
 
 
    /**
     * @return EMongoDB
     * @throws CException
     */
    // protected function getMongoConnection()
    // {
    //     if (($mongo = Yii::app()->getComponent($this->connectionID)) instanceof EMongoDB) {
    //         return $mongo;
    //     } else {
    //         throw new CException("MongoDbLogRoute.connectionID \"{$this->connectionID}\" is invalid.");
    //     }
    // }
}
 
 