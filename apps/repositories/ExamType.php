<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnChapter;
use Learncom\Models\LearnExamType;
use Learncom\Models\LearnGroup;
use Learncom\Models\LearnType;
use Phalcon\Mvc\User\Component;

class ExamType extends Component
{
    public static function findFirstById($id) {
        return LearnExamType::findFirst([
            'type_id  = :id:',
            'bind' => ['id' => $id]
        ]);
    }

    public static function getNameById($id) {
        $model = self::findFirstById($id);
        return $model ? $model->getTypeName() : "";
    }
    public static function getCombobox($type_id) {
        $data = LearnExamType::find(['order' => "type_name ASC"]);
        $output = '';
        foreach ($data as $value) {
            $selected = '';
            if ( $value->getTypeId() == $type_id) {
                $selected = 'selected';
            }
            $output .= "<option " . $selected . " value='" . $value->getTypeId() . "'>" . $value->getTypeName() . "</option>";
        }
        return $output;
    }
}



