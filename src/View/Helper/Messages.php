<?php
namespace Boxspaced\CmsCoreModule\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Messages extends AbstractHelper
{

    /**
     * @return string
     */
    public function __invoke()
    {
        $this->view->flashMessenger()->setMessageOpenFormat('<div class="row"><div class="col-md-12"><div%s><p>');
        $this->view->flashMessenger()->setMessageSeparatorString('</p><p>');
        $this->view->flashMessenger()->setMessageCloseString('</p></div></div></div>');

        $html = '';

        $html .= $this->view->flashMessenger()->renderCurrent('error', ['alert', 'alert-danger']);
        $html .= $this->view->flashMessenger()->renderCurrent('success', ['alert', 'alert-success']);

        $this->view->flashMessenger()->clearCurrentMessagesFromNamespace('error');
        $this->view->flashMessenger()->clearCurrentMessagesFromNamespace('success');

        return $html;
    }

}
