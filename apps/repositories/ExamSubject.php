<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnExamSubject;
use Phalcon\Mvc\User\Component;

class ExamSubject extends Component
{

    public static function findFirstBySubjectId($id) {
        return LearnExamSubject::findFirst([
            'subject_id = :id:',
            'bind' => ['id' => $id]
        ]);
    }
    public static function getComboboxExamSubject($subject_parent_id,$subject_id) {
        $data = LearnExamSubject::find("subject_parent_id = $subject_parent_id");
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
    public static function getNameById($id) {
        $model = self::findFirstBySubjectId($id);
        return $model ? $model->getSubjectName() : "";
    }
}



