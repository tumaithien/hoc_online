<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnExamSubject;
use Learncom\Models\LearnExamType;
use Learncom\Models\LearnSchoolClass;
use Learncom\Models\LearnScore;
use Learncom\Models\LearnTest;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Group;
use Learncom\Repositories\SchooManage;
use Learncom\Repositories\Test;
use Learncom\Repositories\Upload;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class SchoolexamtestController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $sql = "SELECT * FROM Learncom\Models\LearnTest WHERE test_type='exam' AND test_parent_id = 0";
        $arrParameter = array();
        $keyword = $this->request->get('txtSearch');
        if($keyword){
            $sql.=" AND test_name like CONCAT('%',:key_word:,'%') OR test_id = :key_word: ";
            $arrParameter['key_word']=$keyword;
            $this->dispatcher->setParam("txtSearch",$keyword);
        }

        $sql.=" ORDER BY test_id DESC";
        $list_gate = $this->modelsManager->executeQuery($sql,$arrParameter);
        $paginator = new PaginatorModel(array(
            'data'  => $list_gate,
            'limit' => 20,
            'page'  => $current_page,
        ));
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        $this->view->list_item = $paginator->getPaginate();
    }


    public function createAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        set_time_limit(300);
        $data = ['test_order' => 1,'test_class_id' => "",'test_subject_id' => "",'test_active' => "Y",'test_only_one' => "Y",'test_public_score' => "N",'test_is_public_answer' => "N",
            'test_school_class_ids' => "",'test_status'=>"N",'test_group_id' => ''];
        $arrType = LearnExamType::find(['order' => 'type_order ASC']);
        $data['test_subject_id'] = $this->request->get("slcSubject", array('string', 'trim'));
        $data['test_class_id'] = $this->request->get("slcClass", array('string', 'trim'));
        if ($data['test_subject_id'] && $data['test_class_id']) {
            $this->view->pick( $this->dispatcher->getControllerName().'/model');
            $this->view->lock = true;
        } else {
            $this->view->pick( $this->dispatcher->getControllerName().'/beforecreate');
        }
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'test_type' => "exam",
                'test_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'test_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'test_class_id' => $this->request->getPost("slcClass", array('string', 'trim')) ? $this->request->getPost("slcClass", array('string', 'trim')) :  $data['test_class_id'],
                'test_group_id' => $this->request->getPost("slcGroup", array('string', 'trim')),
                'test_time' => $this->request->getPost("txtTime", array('string', 'trim')),
                'test_subject_id' => $this->request->getPost("slcSubject", array('string', 'trim')) ? $this->request->getPost("slcSubject", array('string', 'trim')) : $data['test_subject_id'],
                'test_active' => $this->request->getPost("radActive", array('string', 'trim')),
                'test_number_question' => $this->request->getPost("txtNumberQuestion", array('string', 'trim')),
                'test_public_score' => $this->request->getPost("radIsPublicScore", array('string', 'trim')),
                'test_only_one' => $this->request->getPost("radOnlyOne", array('string', 'trim')),
                'test_is_public_answer' => $this->request->getPost("radIsPublicAnswer", array('string', 'trim')),
                'test_number_exam' => 1,
                'test_description' => trim($this->request->getPost("txtDescription")),
                'test_score_choose' => $this->request->getPost("txtScroreChoose", array('string', 'trim')),
                'test_time_start' => strtotime($this->request->getPost('txtStartTime')),
                'test_parent_id' => 0,
                'test_status' => $this->request->getPost('radStatus'),
            );
            $arrSchoolClass = [];
            if (isset($_POST['slcSchoolClass'])) {
                foreach ($_POST['slcSchoolClass'] as $school_class_id) {
                    array_push($arrSchoolClass,$school_class_id);
                }
            }
            $data['test_school_class_ids'] = implode(',',$arrSchoolClass);

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
            if (empty($messages)) {
                $dataLogTest = [];
                if (isset($_POST['txtTestSubject'])) {
                    $dataLogTest['txtTestSubject'] = $_POST['txtTestSubject'];
                }
                if (isset($_POST['txtTestChapter'])) {
                    $dataLogTest['txtTestChapter'] = $_POST['txtTestChapter'];
                }
                if (isset($_POST['txtTestLesson'])) {
                    $dataLogTest['txtTestLesson'] = $_POST['txtTestLesson'];
                }
                $data['test_array'] = json_encode($dataLogTest,true);
                $new_model = new LearnTest();
                $result = $new_model->save($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật lớp học thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/office/list-new-exam-test");
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
        $select_school_class = LearnSchoolClass::getCheckboxSchoolClass(explode(",",$data['	test_school_class_ids']));
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_class' => $select_class,
            'select_subject' => $select_subject,
            'arrType' => $arrType,
            'arrExamSubject' => $arrExamSubject,
            'select_group' => $select_group,
            'select_school_class' => $select_school_class
        ]);
    }

    public function editAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $id = $this->request->get('id');
        $gateway_model = Test::findFirstById($id);
        if(empty($gateway_model))
        {
            return $this->response->redirect('notfound');
        }
        $data = $gateway_model->toArray();
        $messages = array();
        $arrType = LearnExamType::find(['order' => 'type_order ASC']);
        if($this->request->isPost()) {
            $messages = array();
            $data['test_name'] = $this->request->getPost("txtName", array('string', 'trim'));
            $data['test_order'] = $this->request->getPost("txtOrder", array('string', 'trim'));
            $data['test_group_id'] = $this->request->getPost("slcGroup", array('string', 'trim'));
            $data['test_time'] = $this->request->getPost("txtTime", array('string', 'trim'));
            $data['test_active'] = $this->request->getPost("radActive", array('string', 'trim'));
            $data['test_public_score'] = $this->request->getPost("radIsPublicScore", array('string', 'trim'));
            $data['test_only_one'] = $this->request->getPost("radOnlyOne", array('string', 'trim'));
            $data['test_is_public_answer'] = $this->request->getPost("radIsPublicAnswer", array('string', 'trim'));
            $data['test_description'] = $this->request->getPost("txtDescription", array('string', 'trim'));
            $data['test_score_choose'] = $this->request->getPost("txtScroreChoose", array('string', 'trim'));
            $data['test_time_start'] = $this->request->getPost("txtStartTime", array('string', 'trim'));
            $data['test_status'] = $this->request->getPost("radStatus", array('string', 'trim'));



            $arrSchoolClass = [];
            if (isset($_POST['slcSchoolClass'])) {
                foreach ($_POST['slcSchoolClass'] as $school_class_id) {
                    array_push($arrSchoolClass,$school_class_id);
                }
            }
            $data['test_school_class_ids'] = implode(',',$arrSchoolClass);

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
            if (empty($messages)) {
                $result = $gateway_model->update($data);
                if ($result === true) {
                    if ($gateway_model->getTestStatus() == "S") {
                        SchooManage::updateScoreStatus($gateway_model->getTestId());
                    }
                    $arrTestParent = LearnTest::find([
                        'test_id = :id: OR test_parent_id =:id:',
                        'bind' => ['id' => $id]
                    ]);
                    foreach ($arrTestParent as $test_parent) {
                        $test_parent->setTestGroupId($data['test_group_id']);
                        $test_parent->setTestSchoolClassIds($data['test_school_class_ids']);
                        $test_parent->setTestTimeStart($data['test_time_start']);
                        $test_parent->setTestStatus($data['test_status']);
                        $test_parent->setTestScoreChoose($data['test_score_choose']);
                        $test_parent->setTestTime($data['test_time']);
                        $test_parent->setTestOrder($data['test_order']);
                        $test_parent->setTestOnlyOne($data['test_only_one']);
                        $test_parent->setTestIsPublicAnswer($data['test_is_public_answer']);
                        $test_parent->setTestPublicScore($data['test_public_score']);
                        $test_parent->setTestActive($data['test_active']);
                        $test_parent->setTestDescription($data['test_description']);
                        $test_parent->save();
                    }
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật lớp học thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($gateway_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/office/list-new-exam-test");
            }

        }
        $arrExamSubject = LearnExamSubject::find([
            'subject_parent_id = :parent:',
            'bind' => ['parent' => $data['test_subject_id']],
            'order' => "subject_order ASC"
        ]);

        $array_test  = json_decode($data['test_array'],true);
        $messages["status"] = "border-red";
        $data['mode'] = 'Chỉnh Sửa';
        $select_class = ClassSubject::getComboboxClass($data['test_class_id']);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$data['test_subject_id']);
        $select_group = Group::getComboboxTest($data['test_group_id'],$data['test_subject_id'],$data['test_class_id']);
        $select_school_class = LearnSchoolClass::getCheckboxSchoolClass(explode(",",$data['test_school_class_ids']));
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_class' => $select_class,
            'select_subject' => $select_subject,
            'arrType' => $arrType,
            'arrExamSubject' => $arrExamSubject,
            'select_group' => $select_group,
            'select_school_class' => $select_school_class,
            'test_array' => $array_test,
            'class_name' => ClassSubject::getNameByClassAndSubject($gateway_model->getTestClassId(),$gateway_model->getTestSubjectId())
        ]);
    }
    public function deleteAction()
    {
        $items_checked = $this->request->getPost("item");
        if (!empty($items_checked)) {
            $msg_result = array();
            $count_delete = 0;
            foreach ($items_checked as $id) {
                $test_model = Test::findFirstById($id);
                $arrTestParentId = Test::getArrTestParentId($id);

                if ($test_model) {
                    if ($test_model->delete() === false) {
                        $message_delete = 'Can\'t delete the test name = ' . $test_model->getTestName();
                        $msg_result['status'] = 'error';
                        $msg_result['msg'] = $message_delete;
                    } else {
                        foreach ($arrTestParentId as $test_id) {
                            $arrScore = LearnScore::find([
                                'score_test_id = :test_id:',
                                'bind' => ['test_id' => $test_id]
                            ]);
                            foreach ($arrScore as $score) {
                                $arrLink = $score->getScoreDataNotChoose();
                                $arrLink = explode(',',$arrLink);
                                if (count($arrLink) > 0) {
                                    foreach ($arrLink as $link) {
                                        Upload::deleteSchoolScore($link);
                                    }
                                }
                                $score->delete();
                            }
                            Test::deleteFolder($test_id);
                        }
                        Test::deleteParentId($id);
                        Test::deleteFolder($id);
                        $count_delete++;
                    }
                }
            }
            if ($count_delete > 0) {
                $message_delete = 'Xóa ' . $count_delete . ' Đề Thi thành công.';
                $msg_result['status'] = 'success';
                $msg_result['message'] = $message_delete;
            }
            $this->session->set('msg_result', $msg_result);
            return $this->response->redirect('/office/list-new-exam-test');
        }

    }

}
