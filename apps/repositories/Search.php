<?php

namespace Learncom\Repositories;

use Phalcon\Mvc\User\Component;

class Search extends Component
{
    const FILE_CACHED_CONFIG = "cached_article.txt";
    const FOLDER = "/../messages/";
    const TYPE_KEYWORD  = 'type_keyword';
    const AR_ID = 'ar_id';
    const AR_NAME  = 'ar_name';
    const AR_TITLE  = 'ar_title';
    const AR_KEYWORD = 'ar_keyword';
    const AR_META_KEYWORD = 'ar_meta_keyword';
    const AR_META_DESCRIPTION = 'ar_meta_description';
    const AR_ICON  = 'ar_icon';
    const AR_SUMMARY = 'ar_summary';
    const AR_CONTENT = 'ar_content';
    const AR_TYPE_ID = 'ar_type_id';
    const AR_TYPE_ID_INFORMATION = 1;
    const TYPE_ID_FAQ = 4;
    const TYPE_FAQ = 'faq';
    function __construct()  {
        set_time_limit(0);
        ini_set('memory_limit', -1);
    }
    public function getCache(){
        $arArray = array();
        $cachedConfigFileName =  __DIR__.self::FOLDER.self::FILE_CACHED_CONFIG;
        if (file_exists($cachedConfigFileName)) {
            $arArray = unserialize(file_get_contents($cachedConfigFileName));
        }
        else {
            $list_article =  $this->modelsManager->executeQuery(
                "SELECT a.ar_id,a.ar_name,a.ar_title,a.ar_keyword,a.ar_meta_keyword,a.ar_meta_description,a.ar_icon,a.ar_summary,a.ar_content,a.ar_type_id, t.type_keyword
                 FROM Learncom\Models\LearnArticle a
                 JOIN Learncom\Models\LearnType t ON a.ar_type_id = t.type_id
                 WHERE a.ar_active = 'Y'  
                 ORDER BY a.ar_insert_time ASC           
            ");
            foreach($list_article as $ar) {
                $item = array();
                $item[self::AR_ID] = trim($ar->ar_id);
                $item[self::AR_TYPE_ID] = trim($ar->ar_type_id);
                $item[self::AR_NAME] = trim($ar->ar_name);
                $item[self::TYPE_KEYWORD] = trim($ar->type_keyword);
                $item[self::AR_TITLE] = trim($ar->ar_title);
                $item[self::AR_KEYWORD] = trim($ar->ar_keyword);
                $item[self::AR_META_KEYWORD] = trim($ar->ar_meta_keyword);
                $item[self::AR_META_DESCRIPTION] = trim($ar->ar_meta_description);
                $item[self::AR_ICON] = trim($ar->ar_icon);
                $item[self::AR_SUMMARY] = strip_tags(trim($ar->ar_summary));
                $item[self::AR_CONTENT] = strip_tags(trim($ar->ar_content));
                $arArray[] = $item;
            }
            $folder =__DIR__.self::FOLDER;
            if (!is_dir($folder))  {
                mkdir($folder, 0777,TRUE);
            }
            file_put_contents($cachedConfigFileName, serialize($arArray));
        }
        return $arArray;
    }
    static public function deleteCache(){
        $cachedConfigFileName =  __DIR__.self::FOLDER.self::FILE_CACHED_CONFIG;
        if (file_exists($cachedConfigFileName)) {
            unlink($cachedConfigFileName);
        }
    }
    static public function search($is_faq = false, $ar_type = '', $ar_name = '', $current_page = 1, $length  = 0){
        $search= new Search();
        $ar_name = strtoupper($ar_name);
        $list_article = $search->get_similar($is_faq, $ar_type, $ar_name);
        $offset = ($current_page-1)*$length;
        $list_article_limit = array_slice($list_article,$offset,$length);
        $result = array();
        $type = new Type();
        $arr_faq= $type->getchildren(self::TYPE_ID_FAQ);
        foreach ($list_article_limit as $ar){
            $item = array();
            $item[self::AR_TITLE] = $ar[self::AR_TITLE];
            $item[self::AR_ICON] = $ar[self::AR_ICON];
            switch ($ar[self::AR_TYPE_ID]){
                case self::AR_TYPE_ID_INFORMATION:
                    $item[self::TYPE_KEYWORD] = "";
                    break;
                case  in_array($ar[self::AR_TYPE_ID],$arr_faq):
                    $item[self::TYPE_KEYWORD] = self::TYPE_FAQ;
                    break;
                default:
                    $item[self::TYPE_KEYWORD] = $ar[self::TYPE_KEYWORD];
            }
            $item[self::AR_KEYWORD] = $ar[self::AR_KEYWORD];
            $item[self::AR_META_DESCRIPTION] = $ar[self::AR_META_DESCRIPTION];
            $result[] = $item;
        }
        $research = array();
        $research['count'] = count($list_article);
        $research['result'] = $result;
        return $research;
    }
    function get_similar($is_faq, $ar_type = '',$search = ''){
        $search = $this->replaceword($search);
        $list_article = $this->getCache();
        if($is_faq){
            $list_article = $this->arrfaq($list_article);
        }else if($ar_type){
            $list_article = $this->arrtype($list_article,$ar_type);
        }
        $arr_result = $this->searchword($list_article,$search);
        return $arr_result;
    }
    function arrfaq($list_article){
        $result = array();
        $type = new Type();
        $arr_type_child = $type->get_self_child(0,self::TYPE_ID_FAQ);
        foreach ($list_article as $ar){
            if(in_array($ar[self::AR_TYPE_ID],$arr_type_child)){
                $result[] = $ar;
            }
        }
        return $result;
    }
    function arrtype($list_article,$ar_type = ''){
        $result = array();
        foreach ($list_article as $ar){
            if(($ar[self::AR_TYPE_ID] == $ar_type)){
                $result[] = $ar;
            }
        }
        return $result;
    }
    function searchword($list_article,$words){
        $results = array();
        $list_article_sub = array();
        foreach ($list_article as $ar) {
            if((strpos(strtoupper($ar[self::AR_NAME]),$words) !== false)
              ||(strpos(strtoupper($ar[self::AR_TITLE]),$words) !== false)
              ||(strpos(strtoupper($ar[self::AR_KEYWORD]),$words) !== false)
              ||(strpos(strtoupper($ar[self::AR_META_KEYWORD]),$words) !== false)
              ||(strpos(strtoupper($ar[self::AR_META_DESCRIPTION]),$words) !== false)
              ||(strpos(strtoupper($ar[self::AR_SUMMARY]),$words) !== false)
              ||(strpos(strtoupper($ar[self::AR_CONTENT]),$words) !== false)){
                $results[] = $ar;
            }else {
                $list_article_sub[] = $ar;
            }
        }
        if(strrpos($words," ") !== false){
            $words_sub = substr($words,0,strrpos($words," "));
            $results = array_merge($results,$this->searchword($list_article_sub,$words_sub));
        }
        return $results;
    }
    static function replaceword($words){
        while (strpos($words, '  ') !== false) {
            $words = str_replace('  ', ' ', $words);
        }
        return $words;
    }

}



