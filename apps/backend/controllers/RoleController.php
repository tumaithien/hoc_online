<?php
namespace Learncom\Backend\Controllers;

use Exception;
use Learncom\Models\LearnRole;
use Learncom\Repositories\MessageCheck;
use Learncom\Repositories\User;
use Learncom\Repositories\Role;
use Learncom\Utils\Validator;



class RoleController extends ControllerBase
{
    public function readAction()
    {

        //Statement select list Role
        $sql = "SELECT  role_name , role_id , role_active
              FROM Learncom\Models\LearnRole
              WHERE 1";
        //HTTP request
        $keyword = trim($this->request->get("txtSearchKeyword"));
        $arrParameter = array();
        if ($keyword != NULL) {
            $sql .= " AND role_name like  CONCAT('%',:keyword:,'%')";
            $arrParameter["keyword"] = $keyword;
            $this->dispatcher->setParam("txtSearchKeyword", $keyword);
        }
        //Pagination
        $current_page = $this->request->get("page");
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1) {
            $current_page = 1;
        }
        $limit = 20;
        $start = ($current_page - 1) * $limit;
        $total_role = $this->modelsManager->executeQuery($sql, $arrParameter)->count();
        $sql2 = $sql . "  LIMIT $limit OFFSET $start";
        $list_role_limit = $this->modelsManager->executeQuery($sql2, $arrParameter);
        $total_page = floor($total_role / $limit);
        if (count($total_page) % $limit > 0) {
            $total_page++;
        }
        if ($current_page > $total_page && $current_page != 1) {
            $this->response->redirect("/notfound");
            return;
        }
        $this->dispatcher->setParam("all_record", $total_role);
        $this->dispatcher->setParam("current_page", $current_page);
        $this->dispatcher->setParam("total_page", $total_page);
        $this->dispatcher->setParam("list_role", $list_role_limit);
        $sessionMessages = $this->session->has('message_form') ? $this->session->get('message_form') : array();
        $this->view->message_form = $sessionMessages;
        $this->session->remove('message_form');

    }
    public function createAction()
    {
        $arr_dir = $this->getArrayDir();
        if ($this->request->isPost()) {
            $name = $this->request->get('name');
            $active = $this->request->get('check_active');
            $messageCheck_class = new MessageCheck();
            $message = $messageCheck_class->getMessageRoleKeyword($name, -1);
            $actions = $this->getActions();
            $formData = array(
                'name' => $name,
                'actions' => $actions,
                'active' => $active,
                'type' => 'error',
                'message' => $message
            );
            if (empty($message)) {
                $role_action = new LearnRole();
                $role_action->setRoleActions(serialize($formData['actions']));
                $role_action->setRoleName($formData['name']);
                $role_action->setRoleActive($formData['active']);
                $role_action->save();
                $formData['type'] = "success";
                $formData['message'] = "Tạo New Role thành công";
                $this->session->set(
                    'message_form',
                    array(
                        'type' => $formData['type'],
                        'content' => $formData['message']
                    )
                );
                $this->response->redirect("dashboard/list-role");
            }
        } else {
            $formData = array(
                'name' => '',
                'actions' => '',
                'active' => 'Y',
                'type' => '',
                'message' => ''
            );
        }
        $this->view->formData = $formData;
        $this->view->arr_dir = $arr_dir;
    }
    public function editAction()
    {
        $id = $this->request->get("id");
        $validator = new Validator();
        if ($validator->validInt($id) == false) {
            $this->response->redirect("/notfound");
            return;
        }
        $role_edit = Role::findById($id);
        if (!$role_edit) {
            $this->response->redirect("/notfound");
            return;
        }
        $actions = unserialize($role_edit->getRoleActions());
        $arr_dir = $this->getArrayDir();
        if ($this->request->isPost()) {
            $name = $this->request->get('name');
            $active = $this->request->get('check_active');
            $messageCheck_class = new MessageCheck();
            $message = $messageCheck_class->getMessageRoleKeyword($name, $id);
            $actions = $this->getActions();
            //echo $active;
            $formData = array(
                'id' => $id,
                'name' => $name,
                'actions' => $actions,
                'active' => $active,
                'type' => 'error',
                'message' => $message
            );
            if (empty($message)) {
                $role_edit->setRoleName($formData['name']);
                $role_edit->setRoleActions(serialize($formData['actions']));
                $role_edit->setRoleActive($formData['active']);
                $result_update = $role_edit->save();
                if ($result_update) {
                    $formData['type'] = "success";
                    $formData['message'] = "Cập nhật Role ID = " . $id . " thành công";
                    $this->session->set(
                        'message_form',
                        array(
                            'type' => $formData['type'],
                            'content' => $formData['message']
                        )
                    );
                    $this->response->redirect("dashboard/list-role");
                } else {
                    $formData['message'] = "Cập nhật Role ID = " . $id . " thất bại";
                }
            }
        } else {

            $formData = array(
                'id' => $id,
                'name' => $role_edit->getRoleName(),
                'actions' => $actions,
                'active' => $role_edit->getRoleActive(),
                'type' => '',
                'message' => ''
            );
        }
        $this->view->formData = $formData;
        $this->view->arr_dir = $arr_dir;
    }
    public function deleteAction()
    {

        $id = $this->request->get("id");
        $role = new Role();
        $validator = new Validator();
        if ($validator->validInt($id) == false) {
            $this->response->redirect("/notfound");
            return;
        } else {

            $type = "error";
            $message = "";
            $role_find = role::findById($id);

            if ($role_find) {
                $name = $role_find->getRoleName();
                $user = new User();
                if (!$user->checkUserRole($name)) {

                    $check_delete = $role_find->delete();
                    if ($check_delete) {
                        $type = "success";
                        $message = "Xóa Role Id =" . $id . " thành công";
                        $this->session->set(
                            'message_form',
                            array(
                                'type' => $type,
                                'content' => $message
                            )
                        );
                        $this->response->redirect("dashboard/list-role");
                    } else {
                        $message = "Xóa Role Id =" . $id . " thất bại";
                    }
                } else {
                    $message = "Role " . $name . " exits in User";
                    $this->session->set(
                        'message_form',
                        array(
                            'type' => $type,
                            'content' => $message
                        )
                    );
                }
            } else {
                $this->response->redirect("/notfound");
                return;
            }
            $this->response->redirect("dashboard/list-role?type=" . $type . "&message=" . $message);
        }
    }
    //Funtion get Array directory
    function getArrayDir()
    {
        $arr_dir = array();
        $directory_backend = __DIR__ . "/../../backend/controllers/*.php";
        try {
           
            foreach (glob($directory_backend) as $controller) {
                $className = 'Learncom\Backend\Controllers\\' . basename($controller, '.php');
                $className2 = basename($controller, 'Controller.php');
                if (!strpos($className2, '.php')) {
                    $parent_name = lcfirst($className2);
                    $key = "backend" . $parent_name;
                    if (!isset($arr_dir[$key])) {
                        $arr_dir[$key] = array();
                    }
                    $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);
                    foreach ($methods as $method) {

                        if (strpos($method->name, 'Action')) {
                            $action = basename($method->name, 'Action');
                            
                            $arr_dir[$key][] = $action;
                        }

                    }

                }
            }
        } catch (Exception $e) {
            var_dump($e);
            exit;
        }
        return $arr_dir;
    }
    function getActions()
    {
        $resources = $this->getArrayDir();

        $result = array();
        foreach ($resources as $key => $values) {
            if (!isset($result[$key]))
                $result[$key] = array();
            if (isset($_POST[$key])) {
                for ($i = 0; $i < count($_POST[$key]); $i++) {
                    $result[$key][] = $_POST[$key][$i];
                }
            }
            if (count($result[$key]) == 0) {
                $result[$key][] = "temp";
            }
        }
        return $result;
    }
}