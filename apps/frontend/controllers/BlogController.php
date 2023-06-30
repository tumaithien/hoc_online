<?php
namespace Learncom\Frontend\Controllers;

use Learncom\Repositories\Article;
use Learncom\Repositories\Page;
use Learncom\Utils\Validator;

class BlogController extends ControllerBase
{
    public function indexAction()
    {
        $parent_keyword = 'blogs';
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('blog','Blog');
        $count_sql = "SELECT COUNT(*) AS count ";
        $table_sql = " FROM \Learncom\Models\LearnArticle   
                      WHERE article_active = 'Y'                  
                      ORDER BY article_order ASC
	                  ";
        $select_sql = " SELECT * ";
        $count_query = $this->modelsManager->executeQuery($count_sql.$table_sql);
        $validator = new Validator();
        $current_page = $this->request->getQuery('page', 'int');
        $current_page =isset($current_page)?$current_page:1;
        $totalItems = $count_query[0]->count;
        $check = false;
        if ((isset($current_page))&&($validator->validInt($current_page) == false || $current_page < 1)) {
            $current_page = 1;
            $check = true;
        }
        $itemsPerPage = 6;
        $urlPage = '?';
        if ($urlPage != '?') $urlPage .= '&';
        $urlPattern = $urlPage.'page=(:num)';
        $paginator = new \Paginator($totalItems, $itemsPerPage, $current_page, $urlPattern);
        $offset = ($current_page-1) * $itemsPerPage;
        $limit_sql = " LIMIT ".$offset.",".$itemsPerPage;
        $articles = $this->modelsManager->executeQuery($select_sql.$table_sql.$limit_sql);
        if(($paginator->getNumPages() > 0)&&($current_page > $paginator->getNumPages())){
            $current_page = $paginator->getNumPages();
            $check = true;
        }
        if($check) {
            $urlPage .= 'page=' . $current_page;
            $this->response->redirect('/'.$parent_keyword.$urlPage . '');
            return;
        }
        $paginator->setMaxPagesToShow(6);
        $this->view->setVars([
            'articles' => $articles,
            'parent_keyword' => $parent_keyword,
            'htmlPaginator' => $paginator->toHtmlFrontend(),
        ]);
    }
    public function detailAction(){
        $repoPage = new Page();
        $repoPage->AutoGenMetaPage('blog','Blog');
        $parent_keyword = 'blogs';
        $ar_keyword = $this->dispatcher->getParam("ar-key");
        $repoArticle = new Article();
        $article = $repoArticle->getByKey($ar_keyword);
        if (!$article) {
            $this->my->redirectToNotFoundPage();
            return;
        }

        $article =  $article->toArray();
        $related_articles = $repoArticle->getRelatedArticle($article['article_id']);

        $this->view->setVars([
            'parent_keyword' => $parent_keyword,
            'ar_keyword' => $ar_keyword,
            'article' => $article,
            'menu_bread' => $article['article_name'],
            'related_articles' => $related_articles,
        ]);
    }
}