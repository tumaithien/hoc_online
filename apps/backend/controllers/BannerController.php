<?php
namespace Learncom\Backend\Controllers;
use Learncom\Models\LearnPage;
use Learncom\Repositories\Banner;
use Learncom\Repositories\Log;
use Learncom\Repositories\Upload;
use Learncom\Utils\Validator;
use Learncom\Models\LearnBanner;
use Learncom\Repositories\MessageCheck;

/**
 * @property \GlobalVariable globalVariable
 */
class BannerController extends ControllerBase
{
	public function readAction()
	{
        //Statement select sql
        $sql="SELECT * FROM Learncom\Models\LearnBanner WHERE 1";
		//HTTP Request
		$active=$this->request->get("active_search");
        $arrParameter = array();
		if($active != NULL){
			$sql.=" AND banner_active =:active:";
            $arrParameter['active'] = $active;
			$this->dispatcher->setParam("active_search",$active);
		}
		//Pagination
		$current_page=$this->request->get("page");
        $validator = new Validator();
        if($validator->validInt($current_page)==false || $current_page<1) {
			$current_page=1;
		}
		$limit=20;
		$start=($current_page-1)*$limit;

		$total_banner=$this->modelsManager->executeQuery($sql,$arrParameter)->count();

        $sql2=$sql." ORDER BY banner_id DESC LIMIT $limit OFFSET $start";
        $list_banner_limit =$this->modelsManager->executeQuery($sql2,$arrParameter);
		$total_page=floor($total_banner/$limit);
		if($total_banner%$limit>0){
			$total_page++;
		}
        if($current_page>$total_page && $current_page!=1){
            $this->response->redirect("/notfound");
            return;
        }
        $this->dispatcher->setParam("all_records",$total_banner);
		$this->dispatcher->setParam("current_page",$current_page);
		$this->dispatcher->setParam("total_page",$total_page);
		$this->dispatcher->setParam("list_banner",$list_banner_limit);
	}
	public function createAction()
	{
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $formData = ['active' => 'Y','order' => 1, 'banner_type' => ""];
        $messages = [];
        if( $this->request->isPost() ) {
            $formData = array(
                'name' => $this->request->get('name'),
                'banner_type' => $this->request->get('slcType'),
                'content' => trim($this->request->get('content')),
                'image' => htmlentities($this->request->get('image')),
                'order' => $this->request->get('order'),
                'active' => $this->request->get('check_active'),
            );
            if ($this->request->hasFiles() == true && $_FILES['fileUpload']['size'] > 0)
            {
                $uploadFiles =[];
                $messages_upload = [];
                Upload::uploadFile($uploadFiles,$messages_upload);
                if ($messages_upload['type'] == "success") {
                    $formData['image'] = $uploadFiles[0]['file_url'];
                } else {
                    $messages['image'] = $messages_upload['message'];
                }
            } else {
                if (empty($formData['image'])) {
                    $messages['image'] = "Thông tin này cần thiết.";
                }
            }
            if (empty($formData['banner_type'])) {
                $messages['banner_type'] = "Thông tin này cần thiết.";
            }
            if (empty($formData['name'])) {
                $messages['name'] = "Thông tin này cần thiết.";
            }

            if (empty($formData['order'])) {
                $messages['order'] = "Thông tin này cần thiết.";
            }
            if(empty($messages)) {
                $banner_model = new LearnBanner();
                $banner_model->setBannerName($formData['name']);
                $banner_model->setBannerType($formData['banner_type']);
                $banner_model->setBannerContent($formData['content']);
                $banner_model->setBannerLink($formData['link']);
                $banner_model->setBannerImage($formData['image']);
                $banner_model->setBannerOrder($formData['order']);
                $banner_model->setBannerActive($formData['active']);
                $result_insert = $banner_model->save();
                if ($result_insert) {
                    $formData['type'] = "success";
                    $formData['message'] = "Tạo Banner  thành công";
                    $this->response->redirect("dashboard/list-banner?message=" . $formData['message'] . "&type=" . $formData['type']);
                } else {
                    $formData['message'] = "Tạo Banner  thất bại";
                }
            }
        }
        $messages['status'] = 'border-red';
        $this->view->formData = $formData;
        $this->view->messages = $messages;
	}
    public function editAction()
    {
        $this->view->pick( $this->dispatcher->getControllerName().'/model');
        $id = $this->request->get('id');
        $banner_model = LearnBanner::findFirst([
            'banner_id = :id:',
            'bind' => ['id' => $id]
        ]);
        if(empty($banner_model))
        {
            return $this->response->redirect('notfound');
        }
        $formData =  array(
            'name' => $banner_model->getBannerName(),
            'banner_type' => $banner_model->getBannerType(),
            'content' => $banner_model->getBannerContent(),
            'image' => $banner_model->getBannerImage(),
            'order' => $banner_model->getBannerOrder(),
            'active' => $banner_model->getBannerActive()
        );
        $messages = [];
        $old_image = $formData['image'];
        $upload = false;
        if( $this->request->isPost() ) {
            $formData = array(
                'name' => $this->request->get('name'),
                'banner_type' => $this->request->get('slcType'),
                'content' => trim($this->request->get('content')),
                'image' => htmlentities($this->request->get('image')),
                'order' => $this->request->get('order'),
                'active' => $this->request->get('check_active'),
            );
            if ($this->request->hasFiles() == true && $_FILES['fileUpload']['size'] > 0 )
            {
                $uploadFiles =[];
                $messages_upload = [];
                Upload::uploadFile($uploadFiles,$messages_upload);
                if ($messages_upload['type'] == "success") {
                    $formData['image'] = $uploadFiles[0]['file_url'];
                    $upload = true;
                } else {
                    $messages['image'] = $messages_upload['message'];
                }
            } else {
                if (empty($formData['image'])) {
                    $messages['image'] = "Thông tin này cần thiết.";
                }
            }
            if (empty($formData['banner_type'])) {
                $messages['banner_type'] = "Thông tin này cần thiết.";
            }
            if (empty($formData['name'])) {
                $messages['name'] = "Thông tin này cần thiết.";
            }
            if (empty($formData['order'])) {
                $messages['order'] = "Thông tin này cần thiết.";
            }
            if(empty($messages)) {
                $banner_model->setBannerName($formData['name']);
                $banner_model->setBannerType($formData['banner_type']);
                $banner_model->setBannerContent($formData['content']);
                $banner_model->setBannerLink($formData['link']);
                $banner_model->setBannerImage($formData['image']);
                $banner_model->setBannerOrder($formData['order']);
                $banner_model->setBannerActive($formData['active']);
                $result_insert = $banner_model->save();
                if ($result_insert) {
                    if ($upload) {

                    }
                    $formData['type'] = "success";
                    $formData['message'] = "Cập nhật Banner  thành công";
                    $this->response->redirect("dashboard/list-banner?message=" . $formData['message'] . "&type=" . $formData['type']);
                } else {
                    $formData['message'] = "Cập nhật Banner  thất bại";
                }
            }
        }
        $messages['status'] = 'border-red';
        $this->view->formData = $formData;
        $this->view->messages = $messages;
    }
	public function deleteAction()
	{
		$id=$this->request->get("id");
		$type="error";
		$message="";
		$banner= new Banner();
        $validator = new Validator();
        if($validator->validInt($id)==false)  {
            $this->response->redirect("/notfound");
            return;
        }
        $check_find=$banner->findById($id);
        if($check_find){
            $check_find->delete();
            if($check_find){
                $type="success";
                $message="Xóa Banner ID = ".$id." thành công";
                $log = new Log();
                $log->writefile("Banner action delete ID =".$id,$this->auth['name']);
            }else{
                $message="Xóa Banner ID = ".$id." thất bại";
            }
        }else{
            $this->response->redirect("/notfound");
            return;
        }
		$this->response->redirect("dashboard/list-banner"."?message=".$message."&type=".$type);
	}
}