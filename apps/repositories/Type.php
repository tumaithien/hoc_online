<?php

namespace Learncom\Repositories;
use Phalcon\Mvc\User\Component;
use Learncom\Models\LearnType;
use Learncom\Models\LearnArticle;

class Type extends Component
{
    //Function find a Type By ID
    public function findById($id){
        return LearnType::findFirst("type_id = $id");
    }
    //Function find a Type By type keyword
    public function findTypeKeyWord($strKey){
        return LearnType::findFirst("type_keyword = '$strKey'");
    }
    //Function get Parent of Type
    public function findAllTypeByParent($parent,$country)
    {
        $str="";
        $list_child=NULL;
        if($parent==0){
            $list_child=LearnType::find("type_parent_id =$parent");
        }else{
            $list_child=LearnType::find("type_parent_id =$parent AND type_country_id=$country");
        }
        if($list_child==FALSE){
            return $str.=$parent;
        }else{
            for($i=0;$i<count($list_child);$i++){
                $str.=$list_child[$i]->getTypeId().",";
                $str.= $this->findAllTypeByParent($list_child[$i]->getTypeId(),$country);
            }
        }
        return $str;
    }
    //Function get String for type
    public function findAllTypeByParentHaveRefix($parent,$refix,$country)
    {
        $list_type=NULL;
        if($parent==0){
            $list_type=LearnType ::find("type_parent_id = $parent");
        }else{
            $list_type=LearnType::find("type_parent_id =$parent AND type_country_id = $country");
        }
        $string="";
        foreach($list_type as $type){
            $string.="<option value=".$type->getTypeId().">".$refix."".$type->getTypeName()."</option>";
            $string.=$this->findAllTypeByParentHaveRefix($type->getTypeId(),$refix."......",$country);
        }
        return $string;
    }
    public function getName($id){
        $result="";
        $type=$this->findById($id);
        if($type){
            $result=$type->getTypeName();
        }
        return $result;
    }
    public function delete($id){

        $type = $this->findById($id);
        if($type){
            $list_article = LearnArticle::find("ar_type_id=$id");
            foreach ($list_article as $ar) {
                $ar->delete();
            }
            $type->delete();
        }
    }

    public function getCombo($parent,$refix,$type_search){
        //get list all type is parent
        $sql ="SELECT * FROM Learncom\Models\LearnType WHERE type_parent_id = :parentID: Order By type_order ASC";
        $list_type=$this->modelsManager->executeQuery($sql,
            array(
                "parentID" => $parent
            ));
        $string="";
        foreach($list_type as $type){
            $selected ="";
            if($type->getTypeId()==$type_search){
                $selected ="selected='selected'";
            }
            $string.="<option ".$selected."  value=".$type->getTypeId().">".$refix."".$type->getTypeName()."</option>";

            $string.=$this->getCombo($type->getTypeId(),$refix."......",$type_search);
        }
        return $string;
    }
    public function getComboV2($type_search){
        //get list all type is parent
        $sql ="SELECT * FROM Learncom\Models\LearnType WHERE type_active = 'Y' ORDER BY type_name ASC ";
        $list_type=$this->modelsManager->executeQuery($sql);
        $string="";
        foreach($list_type as $type){
            $selected ="";
            if($type->getTypeKeyword()==$type_search){
                $selected ="selected='selected'";
            }
            $string.="<option ".$selected."  value=".$type->getTypeKeyword().">".$type->getTypeName()."</option>";
        }
        return $string;
    }
    public function findByKeywordv2($type_keyword){
        return LearnType::findFirst(array(
            "conditions" => "type_keyword like :type_keyword: AND type_active = 'Y'",
            "bind"       => array('type_keyword' => $type_keyword),
        ));
    }
    public function getchildren($id){

        $results = array();
        $list_type = LearnType::find("type_parent_id =$id AND type_active = 'Y'");
        foreach($list_type as $type){
            $results[] = $type->getTypeId();
            $results = array_merge(array_merge($results,$this->getchildren($type->getTypeId())));
        }
        return $results;
    }
    public function get_self_child($parent,$id)
    {
        $results = array();
        if($parent){
            $list_type = LearnType::find("type_parent_id =$id AND type_active = 'Y'");
        }else {
            $list_type = LearnType::find("type_id =$id AND type_active = 'Y'");
        }
        foreach($list_type as $type){
            $results[] = $type->getTypeId();
            $results = array_merge(array_merge($results,$this->getchildren($type->getTypeId())));
        }
        return $results;
    }

}




