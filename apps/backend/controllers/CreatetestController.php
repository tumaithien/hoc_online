<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnExamGroup;
use Learncom\Models\LearnExamSubject;
use Learncom\Models\LearnExamType;
use Learncom\Models\LearnTest;
use Learncom\Models\LearnTestFolder;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Document;
use Learncom\Repositories\Exam;
use Learncom\Repositories\ExamGroup;
use Learncom\Repositories\ExamSubject;
use Learncom\Repositories\Group;
use Learncom\Repositories\Test;
use Learncom\Repositories\Upload;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class CreatetestController extends ControllerBase
{
    public function createAction()
    {
        set_time_limit(300);
        $data = ['test_order' => 1,'test_class_id' => "",'test_subject_id' => "",'test_active' => "Y",'test_only_one' => "Y",'test_public_score' => "N",'test_is_public_answer' => "N",
            'test_group_id' => ""];
        $arrType = LearnExamType::find(['order' => 'type_order ASC']);
        $data['test_subject_id'] = $this->request->get("slcSubject", array('string', 'trim'));
        $data['test_class_id'] = $this->request->get("slcClass", array('string', 'trim'));
        if ($data['test_subject_id'] && $data['test_class_id']) {
            $this->view->pick( $this->dispatcher->getControllerName().'/create');
            $this->view->lock = true;
        } else {
            $this->view->pick( $this->dispatcher->getControllerName().'/beforecreate');
        }
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'test_type' => $this->request->getPost("slcType", array('string', 'trim')),
                'test_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'test_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'test_class_id' => $this->request->getPost("slcClass", array('string', 'trim')) ? $this->request->getPost("slcClass", array('string', 'trim')) :  $data['test_class_id'],
                'test_group_id' => $this->request->getPost("slcGroup", array('string', 'trim')),
                'test_time' => $this->request->getPost("txtTime", array('string', 'trim')),
                'test_subject_id' => $this->request->getPost("slcSubject", array('string', 'trim')) ? $this->request->getPost("slcSubject", array('string', 'trim')) : $data['test_subject_id'],
                'test_active' => $this->request->getPost("radActive", array('string', 'trim')),
                'test_public_score' => $this->request->getPost("radIsPublicScore", array('string', 'trim')),
                'test_only_one' => $this->request->getPost("radOnlyOne", array('string', 'trim')),
                'test_is_public_answer' => $this->request->getPost("radIsPublicAnswer", array('string', 'trim')),
                'test_number_exam' => $this->request->getPost("txtNumberOf", array('string', 'trim')),
                'test_description' => trim($this->request->getPost("txtDescription")),
                'test_score_choose' => $this->request->getPost("txtScroreChoose", array('string', 'trim')),
                'test_parent_id' => 0,
                'test_status' => "N",
            );
            if (empty($data['test_name'])) {
                $messages['test_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['test_class_id'])) {
                $messages['test_class_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['test_subject_id'])) {
                $messages['test_subject_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['test_order'])) {
                $messages['test_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['test_order'])) {
                $messages['test_order'] = 'Bạn cần nhập số';
            }
            if (empty($data['test_time'])) {
                $messages['test_time'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['test_time'])) {
                $messages['test_time'] = 'Bạn cần nhập số';
            }
            if (empty($data['test_number_exam'])) {
                $messages['test_number_exam'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['test_number_exam'])) {
                $messages['test_number_exam'] = 'Bạn cần nhập số';
            }
//            foreach ($arrType as $type) {
//                $type_id = $type->getTypeId();
//
//                $numberValue =     $this->request->getPost("sum".$type_id, array('string', 'trim'));
//                if ($numberType && $numberValue) {
//                    $total_number += $numberType;
//                    if ($numberType > $numberValue) {
//                        $messages['type'][$type_id] = "Sô câu không thể lớn hơn trong kho.";
//                    }
//                }
//
//            }

            if (count($messages) == 0) {
                $number_exam_test = $this->request->get('txtNumberExamTest');
                if (!$number_exam_test) {
                    $number_exam_test = 1;
                }
                $arrTestId = [];
                $msg_result = [];
                for ($i = 1; $i <= $number_exam_test; $i++) {
                    $test_repositories = new Test();
                    $msg_result = $test_repositories->createTest($data,$arrTestId);
                }
                 $this->view->setVars([
                      'msg_result' => $msg_result,
                 ]);
                if ($msg_result['status'] == 'success') {
                    $this->view->arrTestId = $arrTestId;
                }
                $this->view->pick($this->dispatcher->getControllerName().'/createsuccess');
            }
        }
        $arrExamSubject = LearnExamSubject::find([
            'subject_parent_id = :parent:',
            'bind' => ['parent' => $data['test_subject_id']],
            'order' => "subject_order ASC"
        ]);

        $messages["status"] = "border-red";
        $data['mode'] = 'Tạo';
        $select_class = ClassSubject::getComboboxClass($data['test_class_id']);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$data['test_subject_id']);
        $select_group = Group::getComboboxTest($data['test_group_id'],$data['test_subject_id'],$data['test_class_id']);
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_class' => $select_class,
            'select_subject' => $select_subject,
            'arrType' => $arrType,
            'arrExamSubject' => $arrExamSubject,
            'select_group' => $select_group
        ]);
    }

    public function getsumexamAction() {
        $class_id = $this->request->getPost('classId');
        $subject_id = $this->request->getPost('subjectId');
        $arrType = LearnExamType::find();
        $arrResult = [];
        foreach ($arrType as $type) {
            $dataExam = [];
            $group = ExamGroup::findFirstByClassSubjectType($class_id,$subject_id,$type->getTypeId());
            if ($group) {
                $dataExam = explode(',',$group->getGroupDataTest());
            }
            $arrResult[$type->getTypeId()] = count($dataExam);
        }
        die(json_encode($arrResult));
    }
    private function recoveAnswer($test) {
        $test = str_replace('&#160;','',$test);
        $test = str_replace(';','',$test);
        while (strpos($test, "\r") !== false ) {
            $test = str_replace("\r","",$test);
        }
        while (strpos($test, "\n") !== false ) {
            $test = str_replace("\n","",$test);
        }
        while (strpos($test, '  ') !== false ) {
            $test = str_replace('  ',' ',$test);
        }
        $test = trim($test);
        $test = str_replace('.',':',$test);
        $test = str_replace(' ',',',$test);
        return $test;
    }

}
