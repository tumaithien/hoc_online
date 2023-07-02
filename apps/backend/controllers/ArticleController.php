<?php

namespace Learncom\Backend\Controllers;

use Learncom\Models\LearnArticle;
use Learncom\Repositories\Article;
use Learncom\Repositories\Chapter;
use Learncom\Repositories\ClassSubject;
use Learncom\Repositories\Document;
use Learncom\Repositories\Upload;
use Learncom\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class ArticleController extends ControllerBase
{
    public function readAction()
    {
        $current_page = $this->request->get('page');
        $key_word = trim($this->request->get("txtSearchKeyword"));
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $sql = "SELECT * FROM Learncom\Models\LearnArticle WHERE 1";
        $arrParameter = array();
        if ($key_word) {
            $sql .= " AND article_name like CONCAT('%',:key_word:,'%') OR article_id = :key_word: ";
            $arrParameter['key_word'] = $key_word;
            $this->dispatcher->setParam("key_word", $key_word);
        }

        $sql .= " ORDER BY article_id DESC";
        $list_gate = $this->modelsManager->executeQuery($sql, $arrParameter);
        $paginator = new PaginatorModel(
            array(
                'data' => $list_gate,
                'limit' => 20,
                'page' => $current_page,
            )
        );
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        $this->view->setVars([
            'list_item' => $paginator->getPaginate(),
        ]);
    }


    public function createAction()
    {
        $this->view->pick($this->dispatcher->getControllerName() . '/model');
        $data = ['article_order' => 1, 'article_active' => "Y"];
        if ($this->request->isPost()) {
            $messages = array();
            $data = array(
                'article_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'article_icon' => $this->request->getPost("txtIcon", array('string', 'trim')),
                'article_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'article_content' => trim($this->request->getPost("txtContent")),
                'article_keyword' => $this->request->getPost("txtKeyword", array('string', 'trim')),
                'article_summary' => trim($this->request->getPost("txtSummary")),
            );
            if (empty($data['article_name'])) {
                $messages['article_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['article_keyword'])) {
                $messages['article_keyword'] = 'Bạn cần nhập thông tin này';
            }

            if (empty($data['article_order'])) {
                $messages['article_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['article_order'])) {
                $messages['article_order'] = 'Bạn cần nhập số';
            }
            $uploadFiles = array();
            $error = [];
            $messagesUpload = [];
            // Check if the user has uploaded files
            if ($this->request->hasFiles() == true) {
                Upload::uploadFile($uploadFiles, $messagesUpload);
            }
            
            $this->view->uploadFiles = $uploadFiles;
            if (!empty($uploadFiles)) {
                $data['article_content'] = $uploadFiles[0]['file_url'];
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_device = new LearnArticle();
                $result = $new_device->save($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Tạo Blog thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_device->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-article");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Tạo';

        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
        ]);
    }

    public function editAction()
    {
        $this->view->pick($this->dispatcher->getControllerName() . '/model');
        $id = $this->request->get('id');
        $gateway_model = Article::findFirstById($id);
        if (empty($gateway_model)) {
            return $this->response->redirect('notfound');
        }
        $data = $gateway_model->toArray();
        $messages = array();
        if ($this->request->isPost()) {
            $data = array(
                'article_name' => $this->request->getPost("txtName", array('string', 'trim')),
                'article_icon' => $this->request->getPost("txtIcon", array('string', 'trim')),
                'article_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'article_keyword' => $this->request->getPost("txtKeyword", array('string', 'trim')),
                'article_summary' => trim($this->request->getPost("txtSummary")),
            );

            if (empty($data['article_name'])) {
                $messages['article_name'] = 'Bạn cần nhập thông tin này';
            }
            if (empty($data['article_keyword'])) {
                $messages['article_keyword'] = 'Bạn cần nhập thông tin này';
            }

            if (empty($data['article_order'])) {
                $messages['article_order'] = 'Bạn cần nhập thông tin này';
            } elseif (!is_numeric($data['article_order'])) {
                $messages['article_order'] = 'Bạn cần nhập số';
            }
            $uploadFiles = array();
            $error = [];
            $messagesUpload = [];
            // Check if the user has uploaded files
            if ($this->request->hasFiles() == true) {
                Upload::uploadFile($uploadFiles, $messagesUpload);
            }
            
            $this->view->uploadFiles = $uploadFiles;
            if (!empty($uploadFiles)) {
                $data['article_content'] = $uploadFiles[0]['file_url'];
            }
            if (count($messages) == 0) {

                $result = $gateway_model->update($data);
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'message' => 'Cập nhật Blog thành công');

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($gateway_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['message'] = $message;
                }

                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-article");
            }
        }
        $messages["status"] = "border-red";
        $data['mode'] = 'Chỉnh Sửa';

        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
        ]);
    }
    public function deleteAction()
    {
        $id = $this->request->get('id');
        $msg_result = array('status' => '', 'message' => '');
        $message = array();
        $type_model = Article::findFirstById($id);
        if ($type_model) {
            if ($type_model->delete() == false) {
                $msg_result['message'] .= 'Can\'t delete the Document: ' . $type_model->getArticleName() . '. Because It\'s exists in: ' . $message . '.<br>';
                $msg_result['status'] = 'error';
            } else {
                $message = 'Xóa Blog ' . $type_model->getArticleName() . ' thành công';
                $msg_result['message'] = $message;
                $msg_result['status'] = 'success';
            }
        }
        $this->session->set('msg_result', $msg_result);
        return $this->response->redirect('/dashboard/list-article');
    }

}