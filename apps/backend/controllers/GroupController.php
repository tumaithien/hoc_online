<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnGroup;
use Learncom\Models\LearnGroupSubject;
use Learncom\Repositories\Chapter;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Group;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class GroupController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $key_word=trim($this->request->get("txtSearchKeyword"));
        $class=trim($this->request->get("slcClass"));
        $subject=trim($this->request->get("slcSubject"));
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $sql = "SELECT * FROM Learncom\Models\LearnGroup WHERE 1";
        $arrParameter = array();
        if($key_word){
            $sql.=" AND group_name like CONCAT('%',:key_word:,'%') OR group_id = :key_word: ";
            $arrParameter['key_word']=$key_word;
            $this->dispatcher->setParam("key_word",$key_word);
        }
        if ($class) {
            $sql.= " AND group_class_id = :class:";
            $arrParameter['class']=$class;
            $this->dispatcher->setParam("slcClass",$class);
        }
        if ($subject) {
            $sql.= " AND group_subject_id = :subject:";
            $arrParameter['subject']=$subject;
            $this->dispatcher->setParam("slcSubject",$subject);
        }
        $sql.=" ORDER BY group_class_id,group_subject_id DESC";
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
            'select_subject' => $select_subject
        ]);
    }


    public function createAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $data = ['group_order' => 1,
            'group_class_id' => "",
            'group_subject_id' => "",
            'group_active' => "Y",
            'group_is_public' => "N",'group_type' => ''];
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'group_type' => $this->request->getPost("slcType", array('string', 'trim')),
                'group_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'group_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'group_class_id' => $this->request->getPost("slcClass", array('string', 'trim')),
                'group_subject_id' => $this->request->getPost("slcSubject", array('string', 'trim')),
                'group_active' => $this->request->getPost("radActive", array('string', 'trim')),
            );
            if (empty($data['group_type'])) {
                $messages['group_type'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['group_name'])) {
                $messages['group_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['group_class_id'])) {
                $messages['group_class_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['group_subject_id'])) {
                $messages['group_subject_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['group_order'])) {
                $messages['group_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['group_order'])) {
                $messages['group_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_device = new LearnGroup();
                $result = $new_device->save($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Tạo chương thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_device->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-group");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Tạo';
        $select_class = ClassSubject::getComboboxClass($data['group_class_id']);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$data['group_subject_id']);
        $select_type = Group::getComboboxType($data['group_type']);
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_class' => $select_class,
            'select_subject' => $select_subject,
            'select_type' => $select_type
        ]);
    }

    public function editAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $id = $this->request->get('id');
        $gateway_model = Group::findFirstById($id);
        if(empty($gateway_model))
        {
            return $this->response->redirect('notfound');
        }
        $data = $gateway_model->toArray();
        $messages = array();
        if($this->request->isPost()) {
            $data = array(
                'group_type' => $this->request->getPost("slcType", array('string', 'trim')),
                'group_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'group_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'group_class_id' => $this->request->getPost("slcClass", array('string', 'trim')),
                'group_subject_id' => $this->request->getPost("slcSubject", array('string', 'trim')),
                'group_active' => $this->request->getPost("radActive", array('string', 'trim')),
                'group_is_public' => $this->request->getPost("radIsPublic", array('string', 'trim')),
            );
            if (empty($data['group_name'])) {
                $messages['group_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['group_type'])) {
                $messages['group_type'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['group_class_id'])) {
                $messages['group_class_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['group_subject_id'])) {
                $messages['group_subject_id'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['group_order'])) {
                $messages['group_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['group_order'])) {
                $messages['group_order'] = 'Bạn cần nhập số';
            }
            if (count($messages) == 0) {

                $result = $gateway_model->update($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật chương thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($gateway_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-group");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Chỉnh Sửa';
        $select_class = ClassSubject::getComboboxClass($data['group_class_id']);
        $select_subject = ClassSubject::getComboboxParentSubject('',0,$data['group_subject_id']);
        $select_type = Group::getComboboxType($data['group_type']);
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_class' => $select_class,
            'select_subject' => $select_subject,
            'select_type' => $select_type
        ]);
    }
    public function deleteAction()
    {
        $id = $this->request->get('id');
        $msg_result = array('error' => '', 'success' => '');
        $type_model = Group::findFirstById($id);
        if ($type_model) {
            if($type_model->delete() == false){
                $msg_result['message'] .= 'Không thể xóa chương: ' . $type_model->getGroupName() ;
                $msg_result['status'] = 'error';
            }
            else {
                $message = 'Xóa tài liệu ' . $type_model->getGroupName() . ' thành công';
                $msg_result['message'] = $message;
                $msg_result['status'] = 'success';
            }
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-group');
    }
    public function getgroupbysubjectAction(){
        $subject_id = $this->request->getPost('subject_id');
        $class_id = $this->request->getPost('class_id');
        $group_id = $this->request->getPost('group_id');
        $html = "";
        if ($class_id && $subject_id) {
            $html = Group::getComboboxVideo($group_id,$subject_id,$class_id);
        }
        die(json_encode($html));

    }
    public function getgroupbysubjecttestAction(){
        $subject_id = $this->request->getPost('subject_id');
        $class_id = $this->request->getPost('class_id');
        $group_id = $this->request->getPost('group_id');
        $html = "";
        if ($class_id && $subject_id) {
            $html = Group::getComboboxTest($group_id,$subject_id,$class_id);
        }
        die(json_encode($html));

    }
    public function getgroupstudentAction() {
        $subjectIds = $this->request->getPost('subjectIds');
        $classIds = $this->request->getPost('classIds');
        $groupIds = $this->request->getPost('dataGroupChecked');
        $arrClassId = explode(',',$classIds);
        $arrSubjectId = explode(',',$subjectIds);
        $arrGroupId = explode(',',$groupIds);
        $output = '';
        foreach ($arrClassId as $class_id) {
            foreach ($arrSubjectId as $subject_id) {
                $arGroup = Group::findByClassAndSubject($class_id,$subject_id);
                if (count($arGroup) > 0) {
                    $output .= "<h4>".ClassSubject::getNameByClassAndSubject($class_id,$subject_id)."</h4>";
                    foreach ($arGroup as $group) {
                        $checked = '';
                        if (in_array($group->getGroupId(),$arrGroupId)) {
                            $checked = "checked";
                        }
                        $output .= "<input style='margin-left:20px' class='check_group check_box' type='checkbox' ".$checked." id='group_". $group->getGroupId()."' name='slcGroup[]' value='".$group->getGroupId()."'>" ." ".addslashes(preg_replace( "/\r|\n/", "", $group->getGroupName())). "<br>";
                    }
                }

            }
        }
        die(json_encode($output));
    }
}
