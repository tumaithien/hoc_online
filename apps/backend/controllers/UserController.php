<?php
namespace Learncom\Backend\Controllers;
use Learncom\Models\LearnUser;
use Learncom\Repositories\User;
use Learncom\Utils\PasswordGenerator;
use Learncom\Utils\Validator;
use Phalcon\Mvc\Model\Query;
class UserController extends ControllerBase
{
    public function readAction()
    {
        //Statement select list Page by $sql
        $txt_keyword=trim($this->request->get("txtSearch"));
        $data = $this->getParameter();
        //Pagination
        $current_page = $this->request->get("page");
        $validator = new Validator();
        if($validator->validInt($current_page)==false || $current_page<1) {
            $current_page=1;
        }
        $limit = 20;
        $start = ($current_page-1)*$limit;
        $total_item=$this->modelsManager->executeQuery($data['sql'],$data['para'])->count();
        $sql2 = $data['sql']." ORDER BY user_name DESC LIMIT $limit OFFSET $start";
        $list_item_limit = $this->modelsManager->executeQuery($sql2,$data['para']);
        $total_page=floor($total_item/$limit);
        if($total_item%$limit>0){
            $total_page++;
        }
        if($current_page>$total_page && $current_page!=1){
            $this->response->redirect("/notfound");
            return;
        }
        $this->dispatcher->setParam("all_records",$total_item);
        $this->dispatcher->setParam("list_item",$list_item_limit);
        $this->dispatcher->setParam("total_page",$total_page);
        $this->dispatcher->setParam("current_page",$current_page);
        $this->dispatcher->setParam("txt_keyword",$txt_keyword);
    }
    public function getParameter(){
        $sql = "SELECT * FROM Learncom\Models\LearnUser                   
                WHERE user_role != 'user'";
        $keyword = trim($this->request->get("txtSearch"));
        $arrParameter = array();
        if($keyword){
            $sql.=" AND (user_name like CONCAT('%',:keyword:,'%') OR user_email like CONCAT('%',:keyword:,'%'))";
            $arrParameter['keyword'] = $keyword;
            $this->dispatcher->setParam("txtSearch",$keyword);
        }
            $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }
    public function createAction()
    {
        $email =$this->request->get('email');
        if( $this->request->isPost() && strlen($email)>0) {
            $user_ro = new User();
            $message= $user_ro->checkEmail($email,-1);
            if(empty($message)) {

                $user_name=$this->request->get("user_name");
                $pass_word=$this->request->get("pass_word");
                $email=$this->request->get("email");
                $role=$this->request->get("select_role");
                $user_model = new LearnUser();
                $user_model->setUserName($user_name);
                $user_model->setUserEmail($email);
                $user_model->setUserPassword($pass_word);
                $user_model->setUserRole($role);
                $check_insert = $user_model->save();
                if($check_insert){
                    $type="success";
                    $message="Tạo New User thành công";
                     $this->response->redirect("dashboard/list-user?message=".$message."&type=".$type);
                }else{
                    $type="error";
                    $message="Tạo New User thất bại";
                }
            }
            $formData = array(
                "user_name" => $this->request->get("user_name"),
                "pass_word" => $this->request->get("pass_word"),
                "email" => $email,
                "role" => $this->request->get("select_role"),
                "type" => 'error',
                "message" => $message
            );
        }else {
            $formData = array(
                "user_name" => '',
                "pass_word" => '',
                "email" => '',
                "role" => '',
                "type" => '',
                "message" => ''
            );
        }
        $this->view->formData = $formData;
    }
    public function editAction()
    {
        $id=$this->request->get("id");
        $validator = new Validator();
        if($validator->validInt($id)==false)  {
            $this->response->redirect("/notfound");
            return;
        }
        $user= new User();
        $check_exit=$user->findById($id);
        if(!$check_exit){
            $this->response->redirect("/notfound");
            return;
        }else{
            $email =$this->request->get('email');
            if( $this->request->isPost() && strlen($email)>0) {

                $user_ro = new User();
                $message= $user_ro->checkEmail($email,$id);

                if(empty($message)){
                    $id=$this->request->get("id");
                    $user_name=$this->request->get("user_name");
                    $pass_word=$this->request->get("pass_word");
                    $email=$this->request->get("email");
                    $tel=$this->request->get("tel");
                    $address=$this->request->get("address");
                    $role=$this->request->get("select_role");

                    $pass_word=$check_exit->getUserPassword();
                    $check_exit->setUserName($user_name);
                    $check_exit->setUserEmail($email);
                    $check_exit->setUserPassword($pass_word);
                    $check_exit->setUserRole($role);
                    $check_update = $check_exit->save();
                    if($check_update){
                        $type="success";
                        $message="Cập nhật User ID = ".$id." thành công";
                        }else{
                        $type="error";
                        $message="Cập nhật User ID = ".$id." thất bại";
                    }
                }
                $formData = array(
                    "id" =>$this->request->get("id"),
                    "user_name" => $this->request->get("user_name"),
                    "pass_word" => $this->request->get("pass_word"),
                    "email" => $email,
                    "tel" => $tel,
                    "address" => $address,
                    "role" => $this->request->get("select_role"),
                    "type" => isset($check_update)?$type:'error',
                    "message" => $message
                );
            }else {
                $formData = array(
                    "id" =>$this->request->get("id"),
                    "user_name" => $check_exit->getUserName(),
                    "pass_word" => $check_exit->getUserPassword(),
                    "email" => $check_exit->getUserEmail(),
                    "role" => $check_exit->getUserRole(),
                    "type" => '',
                    "message" => ''
                );
            }
            $this->view->formData = $formData;
        }

    }
    public function deleteAction()
    {
        $id =(int)$this->request->get("id");
        $validator = new Validator();
        if($validator->validInt($id)==false)  {
            $this->response->redirect("/notfound");
            return;
        }
        $user_repo = new User();
        $findUser = $user_repo->findById($id);
        $deleteUser = $findUser->delete();
        if($deleteUser){
            $type="success";
            $message="Xóa User ID =".$id." thành công";
        }else{
            $type="error";
            $message="Xóa User ID =".$id." thất bại";
        }
        $this->response->redirect("dashboard/list-user?message=".$message."&type=".$type);
        return;
    }
    public function resetAction()
    {
        $id=$this->request->get("id");
        $new_pass=$this->request->get("password");
        $type="error";
        $message="";
        if($new_pass){
            $user= new User();
            $check_user=$user->findById($id);
            if($check_user){
                $check_user->setUserPassword($new_pass);
                $check_update=$check_user->update();

            }else{
                $this->response->redirect("/notfound");
                return;
            }
        }else{
            $message="Please enter a new password if you want reset your password !";
        }
        $this->response->redirect("dashboard/list-user?message=".$message."&type=".$type);
        return;
    }
}