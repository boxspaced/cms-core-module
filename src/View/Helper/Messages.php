<?php
namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Messages extends AbstractHelper
{

    /**
     * @return string
     */
    public function __invoke()
    {
        $this->view->flashMessenger()->setMessageOpenFormat('<div%s><p>');
        $this->view->flashMessenger()->setMessageSeparatorString('</p><p>');
        $this->view->flashMessenger()->setMessageCloseString('</p></div>');

        $html = '';

        $html .= $this->view->flashMessenger()->renderCurrent('error', ['message', 'message-error']);
        $html .= $this->view->flashMessenger()->renderCurrent('success', ['message', 'message-success']);

        $this->view->flashMessenger()->clearCurrentMessagesFromNamespace('error');
        $this->view->flashMessenger()->clearCurrentMessagesFromNamespace('success');

        return $html;
    }

}
