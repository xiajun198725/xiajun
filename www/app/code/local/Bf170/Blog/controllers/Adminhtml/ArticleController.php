<?php

/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User Software Agreement (EULA).
 * It is also available through the world-wide-web at this URL:
 * http://www.bf170.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to eula@bf170.com so we can send you a copy immediately.
 */
class Bf170_Blog_Adminhtml_ArticleController extends Mage_Adminhtml_Controller_Action
{
    
    // 后台访问权限的限制
    protected function _isAllowed()
    {
        // $aclResource = 'blog/manage';
        // return Mage::getSingleton('admin/session')->isAllowed($aclResource);
        return true;
    }

    public function indexAction()
    {
        $this->loadLayout()->_setActiveMenu('blog');
        $this->_addContent($this->getLayout()
            ->createBlock('blog/adminhtml_article_index'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit', 'article', 'blog_adminhtml');
    }

    public function editAction()
    {
        $articleId = $this->getRequest()->getParam('id');
        $article = Mage::getModel('blog/article')->load($articleId);
        
        if (! ! $article->getId()) {
            Mage::register('blog_article', $article);
        }
        
        $this->loadLayout()->_setActiveMenu('blog');
        $this->getLayout()
            ->getBlock('head')
            ->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()
            ->createBlock('blog/adminhtml_article_edit'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        try {
            $postData = $this->getRequest()->getParams();
            $articleId = $this->getRequest()->getParam('id');
            Mage::getSingleton('adminhtml/session')->setBlogArticleFormData($postData);
            $article = Mage::getModel('blog/article')->load($articleId);
            $article->addData($postData);
            $article->save();
            Mage::getSingleton('adminhtml/session')->setBlogArticleFormData(null);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper("blog")->__("信息已保存"));
            $this->_redirect('*/*/edit', array(
                'id' => $article->getId()
            ));
            return;
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper("blog")->__($ex->getMessage()));
            $this->_redirectReferer();
            return;
        }
    }

    public function deleteAction()
    {
        try {
            $articleId = $this->getRequest()->getParam('id');
            $article = Mage::getModel('blog/article')->load($articleId);
            $article->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper("blog")->__("信息已删除"));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper("blog")->__($ex->getMessage()));
        }
        $this->_redirect('*/*/index');
    }
}