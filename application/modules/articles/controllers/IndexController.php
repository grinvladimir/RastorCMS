<?php

class Articles_IndexController extends Rastor_Controller_Action {

    public function indexAction() {
        $articlesModel = new Articles_Model_Article();

        $page = $this->_getParam('page');
        $this->view->paginator = $articlesModel->getPaginator($page, $this->_itemsPerPage, $this->_pageRange, $this->_getLocale()->getLanguage());
        $this->view->headTitle($this->_getTranslator()->_('Статьи'));
    }

    public function viewAction() {
        $articleModel = new Articles_Model_Article();

        $uri = $this->_getParam('uri');

        $article = $articleModel->getDbTable()->getRecordByUri($uri);

        if ($article) {
            $article = $articleModel->buildParams($article, $this->_getLocale()->getLanguage());
            $articleModel->buildHead($article, $this->view);

            $this->view->article = $article;
        } else {
            throw new Exception('Content not found');
        }
    }

}

