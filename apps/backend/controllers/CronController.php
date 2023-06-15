<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnUser;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Role;

class CronController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        $arrUser = LearnUser::find();
        $total = 0;
        foreach ($arrUser as $user) {
            if ($user->getUserSubjectIds()) {
                $arrSubject = explode(',',$user->getUserSubjectIds());
                $newSubject = $arrSubject;
                foreach ($arrSubject as $subject_id) {
                    $subject_model = ClassSubject::findFirstBySubjectId($subject_id);
                    if ($subject_model) {
                        if ($subject_model->getSubjectParentId() == 0) {
                            $arrSubjectParent = ClassSubject::getSubjectByParentId($subject_id);
                            foreach ($arrSubjectParent as $parent) {
                                array_push($newSubject,$parent->getSubjectId());
                            }
                        }
                    }
                }
                $user->setUserSubjectIds(implode(',',$newSubject));
                $result = $user->save();
                if ($result) {
                    $total++;
                }
            }
        }
        echo "total: ".$total." success";
        die();
    }
}
