<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnConfig;
use Phalcon\Mvc\User\Component;

class Config extends Component
{
    const FILE_CACHED_CONFIG = "/../messages/cached_config.txt";
    public function findById($key){
        return LearnConfig::findFirst("config_key ='$key'");
    }
    /* @var LearnConfig $config*/
    public function update($config,$content){
        $key =$config->getConfigKey();
        $phql = "UPDATE Learncom\Models\LearnConfig SET config_content = :content: WHERE config_key = :key:";
        return $this->modelsManager->executeQuery($phql, array(
            'key' => $key,
            'content' => $content
        ));
    }
    public function save($key,$content){
        $phql = "INSERT INTO Learncom\Models\LearnConfig (config_key, config_content) VALUES (:key:, :content:)";
        return $this->modelsManager->executeQuery($phql, array(
            'key' => $key,
            'content' => $content
        ));
    }
    /* @var LearnConfig $config*/
    public function delete($config){
        return $config->delete();
    }

    public function deleteCache(){
        $cachedConfigFileName =  __DIR__.self::FILE_CACHED_CONFIG;
        if (file_exists($cachedConfigFileName)) {
            unlink($cachedConfigFileName);
        }
    }
}



