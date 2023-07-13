<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnVideo;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Video;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class TextController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $key_word=trim($this->request->get("txtSearchKeyword"));
        $class=trim($this->request->get("slcClass"));
        $subject=trim($this->request->get("slcSubject"));
        $group = trim($this->request->get("slcGroup"));
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $sql = "SELECT * FROM Learncom\Models\LearnVideo WHERE video_type='video'";
        $arrParameter = array();
        if($key_word){
            $sql.=" AND video_name like CONCAT('%',:key_word:,'%') OR video_id = :key_word: ";
            $arrParameter['key_word']=$key_word;
            $this->dispatcher->setParam("key_word",$key_word);
        }
        if ($class) {
            $sql.= " AND video_class_id = :class:";
            $arrParameter['class']=$class;
            $this->dispatcher->setParam("slcClass",$class);
        }
        if ($subject) {
            $sql.= " AND video_subject_id = :subject:";
            $arrParameter['subject']=$subject;
            $this->dispatcher->setParam("slcSubject",$subject);
        }
        if ($group) {
            $sql.= " AND video_group_id = :group:";
            $arrParameter['group'] = $group;
            $this->dispatcher->setParam("slcGroup",$group);
        }
        $sql.=" ORDER BY video_class_id,video_subject_id DESC";
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
        $select_class = ClassSubject::getComboboxClass($class);
        $select_subject = ClassSubject::getComboboxParentSubject("",0,$subject);
        $this->view->setVars([
            'list_item' => $paginator->getPaginate(),
            'select_class' => $select_class,
            'select_subject' => $select_subject
        ]);
    }


    public function createAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $data = ['video_order' => 1,'video_class_id' => "",'video_subject_id' => "",'video_active' => "Y"];
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'video_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'video_type' => 'video',
                'video_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'video_link' => $this->request->getPost("txtLink", array('string', 'trim')),
                'video_class_id' => $this->request->getPost("slcClass", array('string', 'trim')),
                'video_subject_id' => $this->request->getPost("slcSubject", array('string', 'trim')),
                'video_active' => $this->request->getPost("radActive", array('string', 'trim')),
                'video_group_id' => $this->request->getPost("slcGroup", array('string', 'trim')),
            );

            if (empty($data['video_name'])) {
                $messages['video_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['video_link'])) {
                $messages['video_link'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['video_class_id'])) {
                $messages['video_class_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['video_subject_id'])) {
                $messages['video_subject_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['video_order'])) {
                $messages['video_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['video_order'])) {
                $messages['video_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_device = new LearnVideo();
                $result = $new_device->save($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Tạo môn học thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_device->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-video");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Tạo';
        $select_class = ClassSubject::getComboboxClass($data['video_class_id']);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$data['video_subject_id']);
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
        $gateway_model = Video::findFirstById($id);
        if(empty($gateway_model))
        {
            return $this->response->redirect('notfound');
        }
        $data = $gateway_model->toArray();
        $messages = array();
        if($this->request->isPost()) {
            $data = array(
                'video_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'video_type' => 'video',
                'video_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'video_link' => $this->request->getPost("txtLink", array('string', 'trim')),
                'video_class_id' => $this->request->getPost("slcClass", array('string', 'trim')),
                'video_subject_id' => $this->request->getPost("slcSubject", array('string', 'trim')),
                'video_active' => $this->request->getPost("radActive", array('string', 'trim')),
                'video_group_id' => $this->request->getPost("slcGroup", array('string', 'trim')),
            );

            if (empty($data['video_name'])) {
                $messages['video_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['video_link'])) {
                $messages['video_link'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['video_class_id'])) {
                $messages['video_class_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['video_subject_id'])) {
                $messages['video_subject_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['video_order'])) {
                $messages['video_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['video_order'])) {
                $messages['video_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {

                $result = $gateway_model->update($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật môn học thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($gateway_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-video");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Chỉnh Sửa';
        $select_class = ClassSubject::getComboboxClass($data['video_class_id']);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$data['video_subject_id']);
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
        $msg_result = array('error' => '', 'success' => '');
        $type_model = Video::findFirstById($id);
        if ($type_model) {
            if($type_model->delete() == false){
                $msg_result['message'] .= 'Can\'t delete the Video: ' . $type_model->getVideoName();
                $msg_result['status'] = 'error';
            }
            else {
                $message = 'Xóa Video ' . $type_model->getVideoName() . ' thành công';
                $msg_result['message'] = $message;
                $msg_result['status'] = 'success';
            }
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-video');
    }

}
