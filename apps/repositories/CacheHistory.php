<?php

namespace Learncom\Repositories;

use Phalcon\Mvc\User\Component;
use Phalcon\Cache\Backend\File as BackFile;
use Phalcon\Cache\Frontend\Data as FrontData;

class CacheHistory extends Component
{
    const PRE_SESSION_CACHE = "";
    const filePath = __DIR__ . "/../../history/";
    static $frontCache = null;
    static $backCache = null;
    public $key = "";
    public $time;
    public function __construct($key = "", $time = 360)
    {
        if ($key) {
            $this->key = $key;
        }
        $this->time = $time;
    }
    public function clearCacheExpried()
    {
        $cacheKeys = self::$backCache->queryKeys();
        foreach ($cacheKeys as $key) {
            $content = self::$backCache->get($key);
            if ($content === null) {
                self::$backCache->delete($key);
            }
        }
    }
    public function getCache()
    {

        $sessionId = self::PRE_SESSION_CACHE . $this->key;

        $cache = $this->getBackCache();
        $cacheKey = self::cacheKeyClients($sessionId);
        $clients = $cache->get($cacheKey);

        return json_decode($clients, true);
    }
    public function deleteCache()
    {
        $sessionId = self::PRE_SESSION_CACHE . $this->key;
        $cache = $this->getBackCache();
        $cacheKey = self::cacheKeyClients($sessionId);

        try {
            if (!is_dir(self::filePath)) {
                mkdir(self::filePath);
            }
            $result = $cache->delete($cacheKey);
        } catch (\Exception $e) {
            return false;
        }

        // $result =  $cache->set($cacheKey);
        return $result;
    }
    public function deleteAllCache()
    {
        $cache = $this->getBackCache();
        $cacheKeys = $cache->queryKeys();
        foreach ($cacheKeys as $key) {
            $result = $cache->delete($key);
        }

    }
    public function setCache($data)
    {
        $sessionId = self::PRE_SESSION_CACHE . $this->key;
        $cache = $this->getBackCache();
        $cacheKey = self::cacheKeyClients($sessionId);
        try {
            if (!is_dir(self::filePath)) {
                mkdir(self::filePath);
            }
            $result = $cache->save($cacheKey, json_encode($data, true));
        } catch (\Exception $e) {
            return false;
        }
        // $result =  $cache->set($cacheKey);
        return $data;
    }
    public function getBackCache()
    {
        $frontCache = $this->getFrontCache();
        if (self::$backCache == null) {
            self::$backCache = new BackFile($frontCache, ['cacheDir' => self::filePath,]);
        }

        return self::$backCache;
    }

    public function getFrontCache()
    {
        if (self::$frontCache == null) {
            self::$frontCache = new FrontData(['lifetime' => $this->time * 24 * 60 * 60]);
        }

        return self::$frontCache;
    }

    //Load cache keys
    public static function cacheKeyClients($sessionId)
    {
        return $sessionId . 'cls.data';
    }
    //custom
    public function setLogDgnl($type_id, $dgnl_id, $is_set)
    {
        $dataOld = [];
        if ($this->getCache()) {
            $dataOld = $this->getCache();
        }
        if ($is_set) {
            if (isset($dataOld[$type_id])) {
                unset($dataOld[$type_id]);
            }
            $dataOld[$type_id] = $dgnl_id;
            $this->setCache($dataOld);
        }
        return $dataOld[$type_id] ?? "";

    }
    public function setLogCourse( $class_id, $subject_id, $group_id, $lesson_id, $is_set)
    {
        $dataOld = [];
        if ($this->getCache()) {
            $dataOld = $this->getCache();
        }
        if ($is_set) {
            if (isset($dataOld[$class_id."_". $subject_id])) {
                unset($dataOld[$class_id."_". $subject_id]);
            }
            $dataOld[$class_id."_". $subject_id] = [
                'group' => $group_id,
                'lesson' => $lesson_id
            ];
            $this->setCache($dataOld);
        }
        return $dataOld[$class_id."_". $subject_id] ?? [];

    }
}