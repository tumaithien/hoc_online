<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnDocument;
use Learncom\Repositories\Chapter;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Document;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class ExcellentdocumentController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $key_word= trim($this->request->get("txtSearchKeyword"));
        $class = trim($this->request->get("slcClass"));
        $subject = trim($this->request->get("slcSubject"));
        $chapter = trim($this->request->get("slcChapter"));
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $sql = "SELECT * FROM Learncom\Models\LearnDocument WHERE document_type = 'excellent'";
        $arrParameter = array();
        if($key_word){
            $sql.=" AND document_name like CONCAT('%',:key_word:,'%') OR document_id = :key_word: ";
            $arrParameter['key_word']=$key_word;
            $this->dispatcher->setParam("key_word",$key_word);
        }
        if ($class) {
            $sql.= " AND document_class_id = :class:";
            $arrParameter['class'] = $class;
            $this->dispatcher->setParam("slcClass",$class);
        }
        if ($subject) {
            $sql.= " AND document_subject_id = :subject:";
            $arrParameter['subject']=$subject;
            $this->dispatcher->setParam("slcSubject",$subject);
        }
        if ($chapter) {
            $sql.= " AND document_chapter_id = :chapter:";
            $arrParameter['chapter'] = $chapter;
            $this->dispatcher->setParam("slcChapter",$chapter);
        }
        $sql.=" ORDER BY document_class_id,document_subject_id DESC";
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
        $data = ['document_order' => 1,'document_class_id' => "",'document_group_ids' => '','document_subject_id' => "",'document_chapter_id' => "",'document_active' => "Y",'document_is_public' => "N"];
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'document_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'document_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'document_link' => $this->request->getPost("txtLink", array('string', 'trim')),
                'document_class_id' => $this->request->getPost("slcClass", array('string', 'trim')),
                'document_subject_id' => $this->request->getPost("slcSubject", array('string', 'trim')),
                'document_active' => $this->request->getPost("radActive", array('string', 'trim')),
                'document_is_public' => $this->request->getPost("radIsPublic", array('string', 'trim')),
                'document_chapter_id' => $this->request->getPost("slcChapter", array('string', 'trim')),
                'document_type' => "excellent"
            );
            $arrGroup = [];
            if (isset($_POST['slcGroupSubject'])) {
                foreach ($_POST['slcGroupSubject'] as $group_id) {
                    array_push($arrGroup,$group_id);
                }
            }
            $data['document_group_ids'] = implode(',',$arrGroup);
            if (empty($data['document_name'])) {
                $messages['document_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['document_link'])) {
                $messages['document_link'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['document_class_id'])) {
                $messages['document_class_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['document_subject_id'])) {
                $messages['document_subject_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['document_order'])) {
                $messages['document_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['document_order'])) {
                $messages['document_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_device = new LearnDocument();
                $result = $new_device->save($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Tạo tài liệu thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_device->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-excellent-document");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Tạo';
        $select_class = ClassSubject::getComboboxClass($data['document_class_id']);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$data['document_subject_id']);
        $select_group = ClassSubject::getCheckboxGroupSubject(explode(',',$data['document_group_ids']));

        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_class' => $select_class,
            'select_subject' => $select_subject,
            'select_group' => $select_group

        ]);
    }

    public function editAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $id = $this->request->get('id');
        $gateway_model = Document::findFirstById($id);
        if(empty($gateway_model))
        {
            return $this->response->redirect('notfound');
        }
        $data = $gateway_model->toArray();
        $messages = array();
        if($this->request->isPost()) {
            $data = array(
                'document_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'document_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'document_link' => $this->request->getPost("txtLink", array('string', 'trim')),
                'document_class_id' => $this->request->getPost("slcClass", array('string', 'trim')),
                'document_subject_id' => $this->request->getPost("slcSubject", array('string', 'trim')),
                'document_active' => $this->request->getPost("radActive", array('string', 'trim')),
                'document_is_public' => $this->request->getPost("radIsPublic", array('string', 'trim')),
                'document_chapter_id' => $this->request->getPost("slcChapter", array('string', 'trim')),
                'document_type' => "excellent"
            );
            $arrGroup = [];
            if (isset($_POST['slcGroupSubject'])) {
                foreach ($_POST['slcGroupSubject'] as $group_id) {
                    array_push($arrGroup,$group_id);
                }
            }
            $data['document_group_ids'] = implode(',',$arrGroup);
            if (empty($data['document_name'])) {
                $messages['document_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['document_link'])) {
                $messages['document_link'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['document_class_id'])) {
                $messages['document_class_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['document_subject_id'])) {
                $messages['document_subject_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['document_order'])) {
                $messages['document_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['document_order'])) {
                $messages['document_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {

                $result = $gateway_model->update($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật tài liệu thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($gateway_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-excellent-document");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Chỉnh Sửa';
        $select_class = ClassSubject::getComboboxClass($data['document_class_id']);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$data['document_subject_id']);
        $select_group = ClassSubject::getCheckboxGroupSubject(explode(',',$data['document_group_ids']));

        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_class' => $select_class,
            'select_subject' => $select_subject,
            'select_group' => $select_group

        ]);
    }
    public function deleteAction()
    {
        $id = $this->request->get('id');
        $msg_result = array('error' => '', 'success' => '');
        $type_model = Document::findFirstById($id);
        if ($type_model) {
            if($type_model->delete() == false){
                $msg_result['message'] .= 'Can\'t delete the Document: ' . $type_model->getDocumentName();
                $msg_result['status'] = 'error';
            }
            else {
                $message = 'Xóa tài liệu ' . $type_model->getDocumentName() . ' thành công';
                $msg_result['message'] = $message;
                $msg_result['status'] = 'success';
            }
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-excellent-document');
    }

}
