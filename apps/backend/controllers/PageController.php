<?php
namespace Learncom\Backend\Controllers;
use Learncom\Models\LearnPage;
use Learncom\Repositories\Page;
use Learncom\Utils\Validator;
use Learncom\Repositories\MessageCheck;
use Learncom\Repositories\Log;

class PageController extends ControllerBase
{
    public function readAction()
    {
        //Statement select list Page by $sql
        $sql="SELECT * FROM Learncom\Models\LearnPage WHERE 1";
        //HTTP Resquest
        $keyword=trim($this->request->get("txtSearch"));
        $arrParameter = array();
        if($keyword){
            $sql.=" AND page_keyword like CONCAT('%',:keyword:,'%')";
            $arrParameter['keyword']=$keyword;
            $this->dispatcher->setParam("txtSearch",$keyword);
        }
        //Pagination
        $current_page=$this->request->get("page");
        $validator = new Validator();
        if($validator->validInt($current_page)==false || $current_page<1) {
            $current_page=1;
        }
        $limit=20;
        $start=($current_page-1)*$limit;
        $count_page=$this->modelsManager->executeQuery($sql,$arrParameter)->count();
        $sql2=$sql." ORDER BY page_id DESC LIMIT $limit OFFSET $start";
        $list_page_limit = $this->modelsManager->executeQuery($sql2,$arrParameter);
        $total_page=floor($count_page/$limit);
        if($count_page%$limit>0){
            $total_page++;
        }
        if($current_page>$total_page && $current_page!=1){
            $this->response->redirect("/notfound");
            return;
        }
        $this->dispatcher->setParam("all_records",$count_page);
        $this->dispatcher->setParam("list_page",$list_page_limit);
        $this->dispatcher->setParam("total_page",$total_page);
        $this->dispatcher->setParam("current_page",$current_page);
    }

    public function createAction()
    {
        if ($this->request->isPost()) {
            $keyword = $this->request->get('keyword');
            $messageCheck_class = new MessageCheck();
            $message = $messageCheck_class->getMessagePageKeyword($keyword, -1);
            $formData = array(
                'name' => $this->request->get('name'),
                'title' => $this->request->get('title'),
                'keyword' => $keyword,
                'metadescription' => $this->request->get('metadiscription'),
                'metakeyword' => $this->request->get('metakeyword'),
                'type' => 'error',
                'message' => $message
            );
            if (empty($message)) {
                $page_model = new LearnPage();
                $page_model->setPageName($formData['name']);
                $page_model->setPageTitle($formData['title']);
                $page_model->setPageKeyword($formData['keyword']);
                $page_model->setPageMetaDescription($formData['metadescription']);
                $page_model->setPageMetaKeyword($formData['metakeyword']);
                $result_insert = $page_model->save();
                if ($result_insert) {
                    $formData['type'] = "success";
                    $formData['message'] = "Tạo New Page thành công";
                    $this->response->redirect("dashboard/list-page?message=" . $formData['message'] . "&type=" . $formData['type']);
                } else {
                    $formData['message'] = "Tạo New Page thất bại";
                }
            }
        } else {
            $formData = array(
                'name' => '',
                'title' => '',
                'keyword' => '',
                'metadescription' => '',
                'metakeyword' => '',
                'type' => '',
                'message' => ''
            );
        }
        $this->view->formData = $formData;
        return;

    }
    public function editAction()
    {
        $id_page= $this->request->get("id");
        $validator = new Validator();
        if($validator->validInt($id_page)==false)  {
            $this->response->redirect("/notfound");
            return;
        }
        $page=LearnPage::findFirst("page_id = $id_page");
        if(!$page){
            $this->response->redirect("/notfound");
            return;
        }else{
            if( $this->request->isPost())
            {
                $pagename =$this->request->get('pagename');
                $title =$this->request->get('title');
                $keyword =$this->request->get('keyword');
                $metadiscription =$this->request->get('metadiscription');
                $metakeyword =$this->request->get('metakeyword');
                $page->setPageName($pagename);
                $page->setPageTitle($title);
                $page->setPageMetaDescription($metadiscription);
                $page->setPageMetaKeyword($metakeyword);
                $page_update = $page->save();
                if($page_update)
                {
                    $type="success";
                    $message="Cập nhật User ID = ".$id_page." thành công";
                }else{
                    $type="error";
                    $message="Cập nhật User ID = ".$id_page." thất bại";
                }
                $formData = array(
                    "id" =>$this->request->get("id"),
                    "page_name" => $pagename ,
                    "page_title" => $title,
                    "page_keyword" => $keyword,
                    "page_metakeyword" => $metakeyword,
                    "page_metadiscription" => $metadiscription,
                    "type" => isset($page_update)?$type:'error',
                    "message" => $message
                );
            }
            else {
                $formData = array(
                    "id" =>$this->request->get("id"),
                    "page_name" => $page->getPageName(),
                    "page_title" => $page->getPageTitle(),
                    "page_keyword" => $page->getPageKeyword(),
                    "page_metakeyword" => $page->getPageMetaKeyword(),
                    "page_metadiscription" => $page->getPageMetaDescription(),
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
        $user_repo=LearnPage::findFirst("page_id = $id");
        $deleteUser = $user_repo->delete();
        if($deleteUser){
            $type="success";
            $message="Xóa Page ID =".$id." thành công";
        }else{
            $type="error";
            $message="Xóa Page ID =".$id." thất bại";
        }
        $this->response->redirect("dashboard/list-page?message=".$message."&type=".$type);
        return;
    }

}
