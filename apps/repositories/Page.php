<?php

namespace Learncom\Repositories;

use Learncom\Models\LearnPage;
use Phalcon\Mvc\User\Component;

class Page extends Component
{
	public function findpage($key)
	{
		return LearnPage::query()
            ->where("page_keyword = '$key'")
            ->execute();
	}
    public function AutoGenMetaPage($page_keyword,$value_default,$in_info = null,$more_value = null)
    {
        $page_object = $this->findPage($page_keyword);
        if(sizeof($page_object) == 0)
        {
            // if not found data in Page
            $this->tag->setTitle($value_default);
            $this->view->meta_key = $value_default;
            $this->view->meta_descript = $value_default;
            // Set menu Active
            $this->view->menu_active = $value_default;
            $this->view->sitebar_active = $page_keyword;
            // Set Breadcum
            $this->view->menu_bread = $value_default;
            //Render info article keyword
            $this->view->id_info = $in_info;

        }
        else{
            $this->tag->setTitle($page_object[0]->getPageTitle().$more_value);
            //Set meta
            $this->view->meta_key = $page_object[0]->getPageMetaKeyword().$more_value;
            $this->view->meta_descript = $page_object[0]->getPageMetaDescription().$more_value;
            $this->view->menu_bread  =  $page_object[0]->getPageName();
            $this->view->menu_id_info  =  $page_object[0]->getPageKeyword();
            // Set menu Active
            $this->view->menu_active = $page_object[0]->getPageKeyword();
            $this->view->sitebar_active = $page_object[0]->getPageKeyword();
            $this->view->id_info = $page_object[0]->getPageKeyword();
        }

    }
    public function findById($id){
        return LearnPage::findFirst("page_id =$id");
    }
}



