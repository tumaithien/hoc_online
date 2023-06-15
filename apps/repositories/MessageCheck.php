<?php
namespace Learncom\Repositories;

use Phalcon\Mvc\User\Component;

class MessageCheck extends Component
{

    public function getMessageRoleKeyword($keyword,$id){
        $result="";
        $sql ="SELECT * FROM Learncom\Models\LearnRole WHERE role_name = :keyID: AND role_id != :ID:";
        $list_role=$this->modelsManager->executeQuery($sql,
            array(
                "keyID" => $keyword,
                "ID"=>$id
            ))->getFirst();
        if($list_role) {
            $result = "Can not Create or Update Role because Name ".$keyword." exists ";
        }
        return $result;
    }
    function getMessageTypeKeyword($keyword,$id,$type_parent){
        $result="";
        $sql ="SELECT * FROM Learncom\Models\LearnType 
              WHERE type_keyword = :keyword: AND type_parent_id =:keyParent: AND type_id != :keyID:";
        $list_type = $this->modelsManager->executeQuery($sql,
            array(
                "keyID" => $id,
                "keyParent"=>$type_parent,
                "keyword" => $keyword
            ))->getFirst();
        if($list_type){
            $result = "Type Keyword Create or Update ".$keyword." exists";
        }
        return $result;
    }
    function getMessagePageKeyword($keyword,$id){
        $result = "";
        $sql = "SELECT * FROM Learncom\Models\LearnPage WHERE page_keyword = :keyword: AND page_id != :keyID:";
        $list_page=$this->modelsManager->executeQuery($sql,
            array(
                "keyword" => $keyword,
                "keyID" => $id
            ))->getFirst();
        if($list_page) {
            $result = "Page Keyword Create or Update ".$keyword." exists.";
        }
        return $result;
    }
    //Check Keyword Article
    public function checkKeyword($ar_keyword,$id,$ar_type){
        $result="";
        $sql = "SELECT * FROM Learncom\Models\LearnArticle
                WHERE ar_keyword = :keyword: AND ar_type_id = :type_id: AND ar_id != :KeyID:";
        $article=$this->modelsManager->executeQuery($sql,
            array(
                "keyword" => $ar_keyword,
                "KeyID" =>$id,
                "type_id" => $ar_type
            ))->getFirst();
        if($article) {
           $result = "Can not Create or Update Aricle because Keyword " . $ar_keyword . " exists ";
        }
        return $result;
    }
    //Check Keyword Faq
    public function checkKeywordFaq($faq_keyword,$id,$faq_type){
        $result="";
        $sql = "SELECT * FROM Learncom\Models\LearnArticle
                WHERE faq_keyword = :keyword: AND faq_type_id = :type_id: AND faq_id != :KeyID:";
        $faq=$this->modelsManager->executeQuery($sql,
            array(
                "keyword" => $faq_keyword,
                "KeyID" =>$id,
                "type_id" => $faq_type
            ))->getFirst();
        if($faq) {
            $result = "Can not Create or Update Aricle because Keyword " . $faq_keyword . " exists ";
        }
        return $result;
    }

}



