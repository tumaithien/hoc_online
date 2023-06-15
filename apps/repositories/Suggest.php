<?php

namespace Learncom\Repositories;

use Phalcon\Mvc\User\Component;
use \Learncom\Models\LearnSuggest;
class Suggest extends Component
{
    const FILE_CACHED_CONFIG = "cached_suggest.txt";
    const FOLDER = "/../messages/";
    const SUG_NAME  = 'sug_name';
    const SUG_COUNT = 'sug_count';
    const PERCENT = 'percent';
    function __construct()  {
        set_time_limit(0);
        ini_set('memory_limit', -1);
    }
    static public function getCache(){
        $sugArray = array();
        $cachedConfigFileName =  __DIR__.self::FOLDER.self::FILE_CACHED_CONFIG;
        if (file_exists($cachedConfigFileName)) {
            $sugArray = unserialize(file_get_contents($cachedConfigFileName));
        }
        else {
            $list_suggest = LearnSuggest::find("1 ORDER BY sug_count DESC");
            foreach($list_suggest as $suggest) {
                $item = array();
                $item[self::SUG_NAME] = $suggest->getSugName();
                $item[self::SUG_COUNT] = $suggest->getSugCount();
                $sugArray[] = $item;
            }
            $folder =__DIR__.self::FOLDER;
            if (!is_dir($folder))  {
                mkdir($folder, 0777,TRUE);
            }
            file_put_contents($cachedConfigFileName, serialize($sugArray));
        }
        return $sugArray;
    }
    static public function deleteCache(){
        $cachedConfigFileName =  __DIR__.self::FOLDER.self::FILE_CACHED_CONFIG;
        if (file_exists($cachedConfigFileName)) {
            $curTime = time();
            $modifiedTime = filemtime($cachedConfigFileName);
            $limit = 3600*24;
            $subTime = $curTime - $modifiedTime;
            if($subTime > $limit ) {
                unlink($cachedConfigFileName);
            }
        }
    }
    static public function update($name){
        $name = strtolower($name);
        $name = str_replace("&quot;","",$name);
        $suggest = LearnSuggest::findFirst(
                                        [
                                            'conditions' => 'sug_name = :name:',
                                            'bind'       => ['name' => $name]
                                        ]);
        if($suggest){
            $suggest->setSugCount($suggest->getSugCount() +1);
            $suggest->save();
        }else{
            $suggest = new LearnSuggest();
            $suggest->setSugName($name);
            $suggest->save();
        }
        self::deleteCache();
    }
    static public function search($name,$limit){
        $suggest = new Suggest();
        $result_temp = $suggest->get_similar($name,$limit);
        $result= array();
        foreach ($result_temp as $sug){
            $result[] = $sug[self::SUG_NAME];
        }
        return $result;
    }
    function get_similar($search,$limit)
    {
        $search = strtoupper($search);
        $result = self::getCache();
        $result_temp = array();
        foreach ($result as $item){
            $name = strtoupper($item[self::SUG_NAME]);
            if(strpos($name,$search) !== false) {
                $result_temp[] = $item;
            }
        }
        $result_sort = $this->array_sort($result_temp, self::SUG_COUNT, SORT_DESC);
        $result_search = array();
        $no=1;
        foreach ($result_sort as $item){
            $result_search[] = $item;
            $no++;
            if($no > $limit){
                break;
            }
        }
        return $result_search;
    }
    function array_sort($array, $on, $order = SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();
        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                }else {
                    $sortable_array[$k] = $v;
                }
            }
            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }
            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }
        return $new_array;
    }
}



