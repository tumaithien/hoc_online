<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnSchoolClass;
use Learncom\Models\LearnScore;
use Learncom\Models\LearnUser;
use Phalcon\Mvc\User\Component;

class User extends Component
{
    /**
     * @param LearnUser $user
     * @return boolean
     */
    public function initSession($user, $role_name, $token_onile)
    {
        if ($user) {
            if (strlen($role_name) == 0) {
                $role_name = 'user';
            }
            $this->session->set(
                'auth',
                array(
                    'id' => $user->getUserId(),
                    'name' => $user->getUserName(),
                    'email' => $user->getUserEmail(),
                    'externalIp' => $user->getUserIp(),
                    'role' => $role_name,
                    'insertTime' => $user->getUserInsertTime(),
                    'online_token' => $token_onile,
                    'class_ids' => explode(',', $user->getUserClassIds()),
                    'subject_ids' => explode(',', $user->getUserSubjectIds()),
                    'group_excluded_ids' => explode(',', $user->getUserGroupExcludedIds()),
                    'group_subject_id' => explode(',', $user->getUserGroupIds()),
                    'school_class_id' => $user->getUserSchoolClassId(),
                )
            );
        }
        return false;
    }
    public function getSchoolClassById($id)
    {
        $model = $this->findById($id);
        if ($model) {
            $class = LearnSchoolClass::getNameById($model->getUserSchoolClassId());
        }
        return $class ? $class : "";
    }
    public function findByEmailPassword($userEmail, $userPassword)
    {
        return LearnUser::findFirst(
            array(
                "user_email=:userEmail: AND user_password=:userPassword:",
                "bind" => array("userEmail" => $userEmail, "userPassword" => $userPassword)
            )
        );
    }
    public function findById($id)
    {
        return LearnUser::findFirst(
            array(
                "user_id=:userID:",
                "bind" => array("userID" => $id)
            )
        );
    }
    public function checkExitMail($mail)
    {
        return LearnUser::findFirst("user_email = '$mail' ");
    }
    public function findUserByIdAndEmail($email, $idUser)
    {
        return LearnUser::query()
            ->where("user_email = '$email'")
            ->andWhere("user_id = $idUser ")
            ->execute();
    }
    public function checkEmail($mail, $id)
    {
        $result = "";
        $user = $this->checkExitMail($mail);
        if ($user) {
            if ($id != $user->getUserId()) {
                $result = "Can not Create or Update User because Email " . $mail . " exists ";
            }
        }
        return $result;
    }
    public static function checkCode($code, $id)
    {
        return LearnUser::findFirst([
            'user_code = :code: AND user_id != :id:',
            'bind' => ['code' => $code, 'id' => $id],
        ]);
    }
    public static function checkExistCode($code)
    {
        return LearnUser::findFirst([
            'user_code = :code:',
            'bind' => ['code' => $code],
        ]);
    }
    public static function checkCreateEmail($email, $id)
    {
        return LearnUser::findFirst([
            'user_email = :email: AND user_id != :id:',
            'bind' => ['email' => $email, 'id' => $id],
        ]);
    }
    public function checkUserRole($rolename)
    {
        $message = '';
        $result = LearnUser::findFirst(
            array(
                "user_role=:rolename: ",
                "bind" => array("rolename" => $rolename)
            )
        );
        if ($result) {
            $message = 'Role name ' . $rolename . ' exit in User';
        }
        return $message;
    }
    //create/update User
    public function saveUser($newUser)
    {
        if ($newUser->save() == true) {
            $result['success'] = true;
            $result['id'] = $newUser->getUserId();
        } else {
            $message = "We can't store your info now: \n";
            foreach ($newUser->getMessages() as $msg) {
                $message .= $msg . "\n";
            }
            $result['success'] = false;
            $result['message'] = $message;
        }
        return $result;
    }
    public static function getComboboxGender($gender)
    {
        $arrGender = [
            'male' => "Nam",
            'female' => "Nữ",
            'default' => "Khác"
        ];
        $output = '';
        foreach ($arrGender as $value => $item) {
            $selected = '';
            if ($value == $gender) {
                $selected = 'selected';
            }
            $output .= "<option " . $selected . " value='" . $value . "'>" . $item . "</option>";
        }
        return $output;
    }
    public static function findByClassAndSubject($class_id, $subject_id)
    {
        return LearnUser::find([
            'user_active = "Y" AND FIND_IN_SET(:class:,user_class_ids) AND FIND_IN_SET(:subject:,user_subject_ids) AND user_role = "user"',
            'bind' => ['class' => $class_id, 'subject' => $subject_id],
            'order' => 'user_name ASC'
        ]);
    }
    public function getNameByID($user_id)
    {
        $model = $this->findById($user_id);
        return $model ? $model->getUserName() : "";
    }
    public function getSchooClassNameByID($user_id)
    {
        $model = $this->findById($user_id);
        return $model ? LearnSchoolClass::getNameById($model->getUserSchoolClassId()) : "";
    }
    public function getIDByID($user_id)
    {
        $model = $this->findById($user_id);
        return $model ? $model->getUserCode() : "";
    }
    public function deleteUser($id)
    {
        $arrScore = LearnScore::find([
            'score_user_id = :id:',
            'bind' => ['id' => $id]
        ]);
        foreach ($arrScore as $score) {
            $arrLink = $score->getScoreDataNotChoose();
            $arrLink = explode(',', $arrLink);
            if (count($arrLink) > 0) {
                foreach ($arrLink as $link) {
                    Upload::deleteSchoolScore($link);
                }
            }
            $score->delete();
        }
    }
    public static function countUser() {
        $cache = new CacheRepo("user_count_user");
        $total = $cache->getCache();
        if(!$total) {
            $total = LearnUser::count([
                'user_role = "user"'
            ]);
            $total = $cache->setCache($total);
        }
        return $total;
        
    }
}