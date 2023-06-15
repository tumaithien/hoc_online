<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnClass;
use Learncom\Models\LearnGroupSubject;
use Learncom\Models\LearnMenu;
use Learncom\Models\LearnSubject;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Menu;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class GroupsubjectController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        if ($this->request->isPost()) {
            $id = $this->request->getPost('sbmSubmit');
            $name = $this->request->getPost('txtName'.$id);
            if (!empty($name)) {
                if ($id == 0) {
                    $model = new LearnGroupSubject();
                } else {
                    $model = LearnGroupSubject::findFirstById($id);
                }
                $model->setGroupName($name);
                $result = $model->save();
                if ($result) {
                    $msg_result = [
                        'status' => 'success',
                        'message' => 'Update Nhóm thành công'
                    ];
                } else {
                    $msg_result = [
                        'status' => 'error',
                        'message' => 'Update Nhóm Thất bại'
                    ];
                }
                $this->view->msg_result = $msg_result;
            }
        }
        $sql = "SELECT * FROM Learncom\Models\LearnGroupSubject WHERE 1";
        $arrParameter = array();
        $sql.=" ORDER BY group_name ASC";
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
        $this->view->setVars([
            'list_item' => $paginator->getPaginate(),
            'start' => true,
        ]);
    }
    public function deleteAction()
    {
        $id = $this->request->get('id');
        $msg_result = array('error' => '', 'success' => '');
        $type_model = LearnGroupSubject::findFirstById($id);
        if ($type_model) {
            if ($type_model->delete() == false) {
                $msg_result['message'] .= 'Can\'t delete the Class: ' . $type_model->getMenuName();
                $msg_result['status'] = 'error';
            }else {
                $message = 'Xóa Nhóm ' . $type_model->getGroupName() . ' thành công';
                $msg_result['message'] = $message;
                $msg_result['status'] = 'success';
            }
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-group-subject');
    }

}
