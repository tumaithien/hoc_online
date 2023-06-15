<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnTest;
use Learncom\Models\LearnTestFolder;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Document;
use Learncom\Repositories\Test;
use Learncom\Repositories\Upload;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class TestController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $key_word= trim($this->request->get("txtSearchKeyword"));
        $class = trim($this->request->get("slcClass"));
        $subject = trim($this->request->get("slcSubject"));
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $sql = "SELECT * FROM Learncom\Models\LearnTest WHERE test_parent_id = 0 AND test_type = 'old' ";
        $arrParameter = array();
        if($key_word){
            $sql.=" AND test_name like CONCAT('%',:key_word:,'%') OR test_id = :key_word: ";
            $arrParameter['key_word']=$key_word;
            $this->dispatcher->setParam("key_word",$key_word);
        }
        if ($class) {
            $sql.= " AND test_class_id = :class:";
            $arrParameter['class'] = $class;
            $this->dispatcher->setParam("slcClass",$class);
        }
        if ($subject) {
            $sql.= " AND test_subject_id = :subject:";
            $arrParameter['subject']= $subject;
            $this->dispatcher->setParam("slcSubject",$subject);
        }

        $sql.=" ORDER BY test_class_id,test_subject_id DESC";
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
        $select_class = ClassSubject::getComboboxClass($class);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$subject);
        $this->view->setVars([
            'list_item' => $paginator->getPaginate(),
            'select_class' => $select_class,
            'select_subject' => $select_subject,
        ]);
    }


    public function createAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $data = ['test_order' => 1,'test_class_id' => "",'test_subject_id' => "",'test_active' => "Y",'test_only_one' => "Y",'test_public_score' => "N",'test_is_public_answer' => "N"];
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'test_type' => "old",
                'test_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'test_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'test_class_id' => $this->request->getPost("slcClass", array('string', 'trim')),
                'test_group_id' => $this->request->getPost("slcGroup", array('string', 'trim')),
                'test_time' => $this->request->getPost("txtTime", array('string', 'trim')),
                'test_link' => $this->request->getPost("image", array('string', 'trim')),
                'test_link_comment' => $this->request->getPost("imageComment", array('string', 'trim')),
                'test_number_question' => $this->request->getPost("txtNumberQuestion", array('string', 'trim')),
                'test_subject_id' => $this->request->getPost("slcSubject", array('string', 'trim')),
                'test_active' => $this->request->getPost("radActive", array('string', 'trim')),
                'test_public_score' => $this->request->getPost("radIsPublicScore", array('string', 'trim')),
                'test_only_one' => $this->request->getPost("radOnlyOne", array('string', 'trim')),
                'test_answer_no' => $this->request->getPost("txtAnswer", array('string', 'trim')),
                'test_answer_decode' => trim($this->request->getPost("txtAnswer")),
                'test_description' => trim($this->request->getPost("txtDescription")),
                'test_is_public_answer' => $this->request->getPost("radIsPublicAnswer", array('string', 'trim')),
                'test_number_exam' => $this->request->getPost("txtNumberOf", array('string', 'trim')),
                'test_score_choose' => $this->request->getPost("txtScroreChoose", array('string', 'trim')),
                'test_status' => "N"
            );
            $answer = $this->recoveAnswer($data['test_answer_no']);
            $arrTestNo = Test::stringToArrAnswer($answer);

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
            if (empty($data['test_number_question'])) {
                $messages['test_number_question'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['test_number_question'])) {
                $messages['test_number_question'] = 'Bạn cần nhập số';
            }
            if (empty($data['test_number_exam'])) {
                $messages['test_number_exam'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['test_number_exam'])) {
                $messages['test_number_exam'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {
                $data['test_answer'] = json_encode($arrTestNo,true);
                $arrOf = [];
                $arrIsChose = [];
                for ($i = 1;$i<=$data['test_number_question'];$i++) {
                    $arrOf[$i] = $i;
                    if (isset($arrTestNo[$i])) {
                        $arrIsChose[$i] = $i;
                    }
                }
                $data['test_array'] = json_encode($arrOf,true);
                $msg_result = array();
                $new_device = new LearnTest();
                $result = $new_device->save($data);
                if ($result === true && $this->request->hasFiles() == true && $_FILES['fileUpload']['size'][0] > 0) {
                    $data_new = $data;
                    $data_new['test_parent_id'] = $new_device->getTestId();
                    if ($data['test_number_exam'] >1) {
                        for ($t=1; $t < $data['test_number_exam'];$t++) {
                            $data_new['test_name'] = $data['test_name']."- MÃ ".$t;
                            $arrTempOf = $arrIsChose;
                            $arrNewOf = [];
                            $v = 1;
                            while ($arrTempOf || $v < count($arrIsChose)) {
                                $k = array_rand($arrTempOf);
                                $arrNewOf[$v] = $arrTempOf[$k];
                                unset($arrTempOf[$k]);
                                $v++;
                            }
                            $arrDataTestNew = [];
                            for ($i = 1;$i<=$data['test_number_question'];$i++) {
                                if (isset($arrNewOf[$i]) && $arrNewOf[$i] != "") {
                                    $arrDataTestNew[$i] = $arrNewOf[$i];
                                } else {
                                    $arrDataTestNew[$i] = $i;
                                }
                            }
                            $data_new['test_array'] = json_encode($arrDataTestNew,true);
                            $new_test = new LearnTest();
                            $new_test->save($data_new);
                        }
                    }
                    //upload hình ảnh
                    $message_image = "";
                    if ($this->request->hasFiles() == true && $_FILES['fileUpload']['size'][0] > 0 )
                    {
                        $total = 0;
                        foreach ($_FILES['fileUpload']['name'] as $item => $value) {
                            $uploadFiles =[];
                            $messages_upload = [];
                            Upload::uploadFileMulti($uploadFiles,$messages_upload,$item,$new_device->getTestId());
                            if ($messages_upload['type'] == "success") {
                                $total++;
                            }
                        }
                        if ($total < count($_FILES['fileUpload']['name'])) {
                            $message_image = "Upload hình ảnh không đủ";
                        }
                    } else {
                        if (empty($formData['test_link'])) {
                            $message_image = "Bạn phải nhập image";
                        }
                    }
                    $msg_result = array('status' => 'success', 'message' => 'Tạo Đề thi thành công '.$message_image);
                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_device->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-test");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Tạo';
        $select_class = ClassSubject::getComboboxClass($data['test_class_id']);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$data['test_subject_id']);
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_class' => $select_class,
            'select_subject' => $select_subject,
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
        if($this->request->isPost()) {
            $old_number_question = $data['test_number_question'];
            $old_test_number_exam = $data['test_number_exam'];
            $data = array(
                'test_type' => "old",
                'test_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'test_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'test_group_id' => $this->request->getPost("slcGroup", array('string', 'trim')),
                'test_class_id' => $this->request->getPost("slcClass", array('string', 'trim')),
                'test_time' => $this->request->getPost("txtTime", array('string', 'trim')),
                'test_link' => $this->request->getPost("image", array('string', 'trim')),
                'test_link_comment' => $this->request->getPost("imageComment", array('string', 'trim')),
                'test_number_question' => $old_number_question,
                'test_subject_id' => $this->request->getPost("slcSubject", array('string', 'trim')),
                'test_active' => $this->request->getPost("radActive", array('string', 'trim')),
                'test_public_score' => $this->request->getPost("radIsPublicScore", array('string', 'trim')),
                'test_only_one' => $this->request->getPost("radOnlyOne", array('string', 'trim')),
                'test_answer_no' => $this->request->getPost("txtAnswer", array('string', 'trim')),
                'test_answer_decode' => trim($this->request->getPost("txtAnswer")),
                'test_description' => trim($this->request->getPost("txtDescription")),
                'test_is_public_answer' => $this->request->getPost("radIsPublicAnswer", array('string', 'trim')),
                'test_number_exam' => $old_test_number_exam,
                'test_score_choose' => $this->request->getPost("txtScroreChoose", array('string', 'trim')),
                'test_status' => "N"
            );
            if (!empty($data['test_answer_no'])) {
                $answer = $this->recoveAnswer($data['test_answer_no']);
                $arrTestNo = Test::stringToArrAnswer($answer);
                if(!$arrTestNo || count($arrTestNo) == 0) {
                    $data['test_answer'] =  $gateway_model->getTestAnswer();
                } else {
                    $data['test_answer'] = json_encode($arrTestNo,true);

                }
            }
            if (empty($data['test_name'])) {
                $messages['test_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['test_score_choose'])) {
                $messages['test_score_choose'] = 'Bạn cần nhập thông tin này';
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
            if (empty($data['test_number_question'])) {
                $messages['test_number_question'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['test_number_question'])) {
                $messages['test_number_question'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {
                $arrOf = [];
                for ($i = 1;$i<=$data['test_number_question'];$i++) {
                    $arrOf[$i] = $i;
                }
                $data['test_array'] = json_encode($arrOf,true);
                $result = $gateway_model->update($data);
                if ($result === true) {
                    $is_new = false;
//                    if ($old_number_question != $data['test_number_question'] || $old_test_number_exam != $data['test_number_exam']) {
//                        $is_new = true;
//                        Test::deleteParentId($gateway_model->getTestId());
//                        $data_new = $data;
//                        $data_new['test_parent_id'] = $gateway_model->getTestId();
//                        if ($data['test_number_exam'] > 1) {
//                            for ($t = 1; $t < $data['test_number_exam']; $t++) {
//                                $data_new['test_name'] = $data['test_name'] . "- MÃ " . $t;
//                                $arrTempOf = $arrOf;
//                                $arrNewOf = [];
//                                $v = 1;
//                                while (count($arrTempOf) > 0 || $v <= $data['test_number_exam']) {
//                                    $k = array_rand($arrTempOf);
//                                    $arrNewOf[$v] = $arrTempOf[$k];
//                                    unset($arrTempOf[$k]);
//                                    $v++;
//                                }
//                                $data_new['test_array'] = json_encode($arrNewOf, true);
//                                $new_test = new LearnTest();
//                                $new_test->save($data_new);
//                            }
//                        }
//
//                    }
                    //upload hình ảnh
                    $message_image = "";
                    if ($this->request->hasFiles() == true && $_FILES['fileUpload']['size'][0] > 0 )
                    {
                        $is_new = true;
                        Upload::deleteDirTest($gateway_model->getTestId());
                        Test::deleteFolder($gateway_model->getTestId());
                        $total = 0;
                        foreach ($_FILES['fileUpload']['name'] as $item => $value) {
                            $uploadFiles =[];
                            $messages_upload = [];
                            Upload::uploadFileMulti($uploadFiles,$messages_upload,$item,$gateway_model->getTestId());
                            if ($messages_upload['type'] == "success") {
                                $total++;
                            }
                        }
                        if ($total < count($_FILES['fileUpload']['name'])) {
                            $message_image = "Upload hình ảnh không đủ";
                        }
                    } else {
                        if (empty($formData['test_link'])) {
                            $message_image = "Bạn phải nhập image";
                        }
                    }
                    if ($is_new == false) {
                        $parent_test = LearnTest::find([
                            "test_parent_id = $id"
                        ]);
                        $data_new = $data;
                        $data_new['test_parent_id'] = $gateway_model->getTestId();
                        foreach ($parent_test as $key => $test) {
                            $data_new['test_name'] = $data['test_name'] . " Số " . $key;
                            $data_new['test_array'] = $test->getTestArray();
                            $data_new['test_id'] = $test->getTestId();
                            $test->save($data_new);
                        }
                    }
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật Đề thi thành công '.$message_image);

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($gateway_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-test");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Chỉnh Sửa';
        $select_class = ClassSubject::getComboboxClass($data['test_class_id']);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$data['test_subject_id']);
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_class' => $select_class,
            'select_subject' => $select_subject,
        ]);
    }
    public function deleteAction()
    {
        $id = $this->request->get('id');
        $msg_result = array('status' => '', 'message' => '');
        $test_model = Test::findFirstById($id);
        $arrTestParentId = Test::getArrTestParentId($id);
        if ($test_model) {
            if($test_model->delete() == false){
                $msg_result['message'] .= 'Can\'t delete the Document: ' . $test_model->getTestName();
                $msg_result['status'] = 'danger';
            }
            else {
                Test::deleteParentId($id);
                Test::deleteFolder($id);
                Upload::deleteDirTest($id);
                //delete bài làm
                foreach ($arrTestParentId as $test_id) {
                    $test_parent_model = Test::findFirstById($test_id);
                    if ($test_parent_model) {
                        $score = $test_parent_model->score;
                        if (count($score->toArray()) > 0) {
                            $score->delete();
                        }
                    }

                }
                $message = 'Xóa Đê ' . $test_model->getTestName() . ' thành công';
                $msg_result['message'] = $message;
                $msg_result['status'] = 'success';
            }
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-test');
    }
    public function detailAction() {
        $id = $this->request->get('id');
        $test_model = Test::findFirstById($id);
        $arrTestFolder = LearnTestFolder::find([
            'folder_test_id = :test_id:',
            'bind' => ['test_id' => $id],
            'order' => "folder_status ASC"
        ]);
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;

        $paginator = new PaginatorModel(array(
            'data'  => $arrTestFolder,
            'limit' => 10,
            'page'  => $current_page,
        ));
        $arrAnserwer = json_decode($test_model->getTestAnswer(),true);
        $this->view->setVars([
            'list_item' => $paginator->getPaginate(),
            'arrAnserwer' => $arrAnserwer,
            'id' => $id
        ]);
    }
    public function editdetailAction() {
        $id = $this->request->get('id');
        $folder_model = LearnTestFolder::findFirst([
            'folder_id = :id:',
            'bind' => ['id' => $id],
        ]);
        $test_id = $this->request->get('testId');
        $test_model = Test::findFirstById($test_id);
        $messages = [];
        $arrAnserwer = json_decode($test_model->getTestAnswer(),true);
        if($this->request->isPost()) {
            $anserwer = $this->request->getPost('txtAnswer', array('string', 'trim'));
            if ($anserwer) {
                $arrAnserwer[$folder_model->getFolderStatus()] = $anserwer;
                $test_model->setTestAnswer(json_encode($arrAnserwer),true);
                $test_model->save();
            }
            if ($this->request->hasFiles() == true && $_FILES['fileUpload']['size'] > 0)
            {
                $uploadFiles =[];
                $messages_upload = [];

                Upload::uploadFileTest($uploadFiles,$messages_upload,$test_id,true);
                if ($messages_upload['type'] == "success") {
                   $link_test = $uploadFiles[0]['file_url'];
                   Upload::deleteDirExam($folder_model->getFolderLink());
                    $folder_model->setFolderLink($link_test);
                } else {
                    $messages['mesage'][1] = "Câu hỏi: ".$messages_upload['message'];
                }
            }
            if ($this->request->hasFiles() == true && $_FILES['fileUploadComment']['size'] > 0)
            {
                $uploadFilesComment =[];
                $messages_upload = [];
                Upload::uploadFileTest($uploadFilesComment,$messages_upload,$test_id,false);
                if ($messages_upload['type'] == "success") {
                    $link_test_answer = $uploadFilesComment[0]['file_url'];
                    Upload::deleteDirExam($folder_model->getFolderLinkComment());
                    $folder_model->setFolderLinkComment($link_test_answer);
                } else {
                    $messages['mesage'][2] = "Lời giải: " .$messages_upload['message'];
                }
            }
            if(empty($messages['mesage'])) {
                $result = $folder_model->save();
                if (!$result) {
                    $messages['mesage'][3] = "lưu không thành công";
                }
            }
            if (empty($messages['mesage'])) {
                $messages['status'] = "success";
                $messages['mesage'][4] = "Sửa câu hỏi thành công";
            } else {
                $messages['status'] = "error";
            }
        }

        $this->view->setVars([
            'arrAnserwer' => $arrAnserwer,
            'test_id' => $test_id,
            'messages' => $messages
        ]);
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
