<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnRole;
use Phalcon\Mvc\User\Component;
class Role extends Component
{
    public static function findById($id){
        return LearnRole::findFirst("role_id = $id");
    }
    public function  getRoleAction(){
        $sql ="SELECT role_name ,role_actions  
               FROM Learncom\Models\LearnRole
               WHERE role_active = 'Y'";
        $list_role = $this->modelsManager->executeQuery($sql);
        return $list_role;
    }
    public static  function getString($numOfCols,$resources,$values,$prefix){
        $result = "";
        $rowCount = 0;
        $bootstrapColWidth = 12 / $numOfCols;
        $result ="<div class='row-fluid'>";
        foreach ($resources as $key => $items) {
            $name = str_replace($prefix,"",$key);
            $result .= "<div class='span" . $bootstrapColWidth . "'>
            <div class='well'><strong style='text-transform: uppercase;'>" . $name ."</strong>";
            foreach ($items as $item) {
                $seletced ="";
                if (isset($values[$key]) && in_array($item, $values[$key])) $seletced = "checked='checked'";
                $result .= "<div><input type='checkbox' ".$seletced." id='".$key.$item."' name='".$key."[]' value='".$item."'/>   ".
                    $item."                                 
                            </div>";
            }
            $result .="</div></div>";
            $rowCount++;
            if($rowCount % $numOfCols == 0) $result .="</div><div class='row-fluid'>";
        }
        $result .="</div>";
        return $result;
    }
    public static function getGuestUser(){
        return array('guest', 'user');
    }
    public static  function getAllActive()
    {
        return LearnRole::find("role_active ='Y'");
    }
    public static function checkMenu($role_action,$role_default){
        $result = false;
        foreach ($role_default as $controller) {
            if(isset($role_action[$controller])&& in_array('read',$role_action[$controller])) {
                $result = true;
                break;
            }
        }
        return $result;
    }
    public static function checkMenuSub($role_action,$role_current){
        $result = false;
        $pre = "backend";

        if(in_array($pre.$role_current, $role_action)){
            $result = true;
        }
        return $result;
    }
    public static function checkiMenuItem($role_action,$role_current){
        $result = false;
        $pre = "backend";
        if(isset($role_action[$pre.$role_current])&& in_array('read',$role_action[$pre.$role_current])) {
            $result = true;
        }
        return $result;
    }
    public static function getFirstByName($role_name){
        return LearnRole::findFirst(array(
            'role_name = :role_name:',
            'bind' => array('role_name' => $role_name)
        ));
    }
    public static function getControllers(){
        return array(

            'top_destination' => array(
                'backend',
                'backendtopdestination'
            ),
        );
    }
}



