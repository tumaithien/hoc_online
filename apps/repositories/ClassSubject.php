<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnClass;
use Learncom\Models\LearnExamSubject;
use Learncom\Models\LearnGroupSubject;
use Learncom\Models\LearnSubject;
use Phalcon\Mvc\User\Component;
use \Learncom\Models\LearnArticle;

class ClassSubject extends Component
{
    public static function findFirstByClasId($id) {
        return LearnClass::findFirst([
            'class_id = :id:',
            'bind' => ['id' => $id]
        ]);
    }
    public static function findFirstClassNameById($id) {
        $result = LearnClass::findFirst([
            'class_id = :id:',
            'bind' => ['id' => $id]
        ]);
        return $result ? $result->getClassName() : '';
    }
    public static function findFirstBySubjectId($id) {
        return LearnSubject::findFirst([
            'subject_id = :id:',
            'bind' => ['id' => $id]
        ]);
    }
    public static function findFirstSubjectNameById($id) {
        $result = LearnSubject::findFirst([
            'subject_id = :id:',
            'bind' => ['id' => $id]
        ]);
        return $result ? $result->getSubjectName() : '';
    }
    public static function getComboboxClass($class_id) {
        $data = LearnClass::find();
        $output = '';
        $class_ids = explode(',',$class_id);
        foreach ($data as $value) {
            $selected = '';
            if (in_array($value->getClassId(),$class_ids)) {
                $selected = 'selected';
            }
            $output .= "<option " . $selected . " value='" . $value->getClassId() . "'>" . $value->getClassName() . "</option>";
        }
        return $output;
    }
    public static function getComboboxParentSubject($str = "", $parent=0, $inputslc)
    {
        $data = LearnSubject::find([
           'subject_parent_id= :parent:',
           'bind' => ['parent' => $parent]
        ]);

        $output="";
        foreach ($data as $key => $value)
        {
            $selected ="";
            if($value->getSubjectId() === $inputslc)
            {
                $selected ="selected='selected'";
            }
            $output.= "<option ".$selected." value='".$value->getSubjectId()."'>".$str.$value->getSubjectName()."</option>";
            $output.= self::getComboboxParentSubject($str."----", $value->getSubjectId(), $inputslc);

        }
        return $output;
    }
    public static function getComboboxSubject($subject_id) {
        $data = LearnSubject::find();
        $output = '';
        $subject_ids = explode(',',$subject_id);
        foreach ($data as $value) {
            $selected = '';
            if (in_array($value->getSubjectId(),$subject_ids)) {
                $selected = 'selected';
            }
            $output .= "<option " . $selected . " value='" . $value->getSubjectId() . "'>" . $value->getSubjectName() . "</option>";
        }
        return $output;
    }
    public  static function getCheckboxClass ($arrChecked,$id = -1){
        $output = '';
        $dataService =  LearnClass::find();
        foreach ($dataService as $value){
            $checked = '';
            if (in_array($value->getClassId(),$arrChecked)) {
                $checked = 'checked';
            }
            if ($id != -1) {
                $output .= "<input class='check_class check_box search_group' type='checkbox' ".$checked." id='class_".$id."_". $value->getClassId()."' name='slcClass[".$id."][]' value='".$value->getClassId()."'>" ." ".addslashes(preg_replace( "/\r|\n/", "", $value->getClassName())). "<br>";
            } else {
                $output .= "<input class='check_class check_box search_group' type='checkbox' ".$checked." id='class_". $value->getClassId()."' name='slcClass[]' value='".$value->getClassId()."'>" ." ".addslashes(preg_replace( "/\r|\n/", "", $value->getClassName())). "<br>";
            }
        }
        return $output;
    }
    public  static function getCheckboxSubject ($arrChecked,$id = -1){
        $output = '';
        $dataService =  LearnSubject::find("subject_parent_id = 0");
        foreach ($dataService as $value){
            $checked = '';
            if (in_array($value->getSubjectId(),$arrChecked)) {
                $checked = 'checked';
            }
            if ($id != -1) {
                $output .= "<input data-browse='parent_" . $value->getSubjectId() . "' class='check_subject check_box search_group parent' type='checkbox' " . $checked . " id='subject_".$id."_". $value->getSubjectId() . "' name='slcSubject[".$id."][]' value='" . $value->getSubjectId() . "'>" . " " . addslashes(preg_replace("/\r|\n/", "", $value->getSubjectName())) . "<br>";
            } else {
                $output .= "<input data-browse='parent_" . $value->getSubjectId() . "' class='check_subject check_box search_group parent' type='checkbox' " . $checked . " id='subject_" . $value->getSubjectId() . "' name='slcSubject[]' value='" . $value->getSubjectId() . "'>" . " " . addslashes(preg_replace("/\r|\n/", "", $value->getSubjectName())) . "<br>";
            }
            $arrParent = self::getSubjectByParentId($value->getSubjectId());
            foreach ($arrParent as $parent) {
                $checked_2 = '';

                if (in_array($parent->getSubjectId(),$arrChecked)) {
                    $checked_2 = 'checked';
                }

                if ($id != -1) {
                    $output .= "<input class='check_subject check_box search_group parent_".$value->getSubjectId()."' type='checkbox' ".$checked_2." id='subject_".$id."_". $parent->getSubjectId()."' name='slcSubject[".$id."][]' value='".$parent->getSubjectId()."'>" ." ---".addslashes(preg_replace( "/\r|\n/", "", $parent->getSubjectName())). "<br>";
                } else {
                    $output .= "<input class='check_subject check_box search_group parent_".$value->getSubjectId()."' type='checkbox' ".$checked_2." id='subject_". $parent->getSubjectId()."' name='slcSubject[]' value='".$parent->getSubjectId()."'>" ." ---".addslashes(preg_replace( "/\r|\n/", "", $parent->getSubjectName())). "<br>";
                }
            }
        }
        return $output;
    }
    public  static function getCheckboxGroupSubject ($arrChecked,$id = -1){
        $output = '';
        $dataService =  LearnGroupSubject::find();
        foreach ($dataService as $value){
            $checked = '';
            if (in_array($value->getGroupId(),$arrChecked)) {
                $checked = 'checked';
            }
            if ($id != -1) {
                $output .= "<input data-browse='parent_" . $value->getGroupId() . "' class='check_subject check_box search_group parent' type='checkbox' " . $checked . " id='group_".$id."_". $value->getGroupId() . "' name='slcGroupSubject[".$id."][]' value='" . $value->getGroupId() . "'>" . " " . addslashes(preg_replace("/\r|\n/", "", $value->getGroupName())) . "<br>";

            } else {
                $output .= "<input data-browse='parent_" . $value->getGroupId() . "' class='check_subject check_box search_group parent' type='checkbox' " . $checked . " id='group_" . $value->getGroupId() . "' name='slcGroupSubject[]' value='" . $value->getGroupId() . "'>" . " " . addslashes(preg_replace("/\r|\n/", "", $value->getGroupName())) . "<br>";
            }
        }
        return $output;
    }
    public static function getNameClassByIds($ids) {
        $arrId = explode(',',$ids);
        $name = [];
        foreach ($arrId as $id ){
            $model = self::findFirstByClasId($id);
            if ($model) {
                array_push($name,$model->getClassName());
            }
        }
        return implode(', ',$name);
    }
    public static function getNameSubjectByIds($ids) {
        $arrId = explode(',',$ids);
        $name = [];
        foreach ($arrId as $id ){
            $model = self::findFirstBySubjectId($id);
            if ($model) {
                array_push($name,$model->getSubjectName());
            }
        }
        return implode(', ',$name);
    }
    public static function getSubjectByParentId($id) {
        return LearnSubject::find([
            'subject_parent_id = :id:',
            'bind' => ['id' => $id],
            'order' => 'subject_order ASC'
        ]);
    }
    public static function getNameByClassAndSubject($class_id,$subject_id) {
        $class_name = self::getNameClassByIds($class_id);
        $subject_name = self::getNameSubjectByIds($subject_id);
        return $subject_name." ".$class_name;
    }
    public static function countUserByClassAndSubject($class_id,$subject_id) {
        if ($class_id) {

        }
    }
    public static function getSubjectImage($id) {
        $model = self::findFirstBySubjectId($id);
        return $model ? $model->getSubjectImage() : "";
    }
 }



