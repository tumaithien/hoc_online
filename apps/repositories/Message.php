<?php

namespace Learncom\Repositories;

use Phalcon\Mvc\User\Component;
use \Learncom\Models\LearnConfig;

class Message extends Component
{
    public function save($key,$content){
        $phql = "INSERT INTO Learncom\Models\LearnConfig (config_key, config_content) VALUES (:key:, :content:)";
         return $this->modelsManager->executeQuery($phql, array(
             'key' => $key,
             'content' => $content
         ));
    }
    public function findByID($key){
        return LearnConfig::findFirst("config_key ='$key'");
    }
    public function update($config,$content){
        $key =$config->getConfigKey();
        $phql = "UPDATE Learncom\Models\LearnConfig SET config_content = :content: WHERE config_key = :key:";
        return $this->modelsManager->executeQuery($phql, array(
            'key' => $key,
            'content' => $content
        ));
    }
    public function delete($config){
        return $config->delete();
    }
    public static function getCache(){
        $cachedConfigFileName =  __DIR__."/../messages/cached_config.txt";
        if (file_exists($cachedConfigFileName)) {
            $messageArray = unserialize(file_get_contents($cachedConfigFileName));
        }
        else {
            $messages = LearnConfig::find();
            $messageArray = [];
            foreach($messages as $message) {
                $messageArray[$message->getConfigKey()] = $message->getConfigContent();
            }

            file_put_contents($cachedConfigFileName, serialize($messageArray));
        }

        foreach($messageArray as $key => $value) {
            if (!defined($key)) {
                define($key, $value);
            }
        }
    }
    public static function deleteCache(){
        $cachedConfigFileName =  __DIR__."/../messages/cached_config.txt";
        if (file_exists($cachedConfigFileName)) {
            unlink($cachedConfigFileName);
        }
    }

}



