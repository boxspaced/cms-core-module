<?php
namespace Boxspaced\CmsCoreModule;

use Zend\Log\Logger;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend_Search_Lucene_Search_QueryParser as SearchQueryParser;
use Zend_Search_Lucene_Analysis_Analyzer as SearchAnalyzer;
use Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive as SearchAnalyzerCaseInsensitive;

class Module
{

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * @param MvcEvent $event
     * @return void
     */
    public function onBootstrap(MvcEvent $event)
    {
        $eventManager = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();

        $eventManager->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this, 'error']
        );

        $sharedEventManager->attach(
            AbstractActionController::class,
            MvcEvent::EVENT_RENDER,
            function (MvcEvent $event) {

                $controller = $event->getTarget();
                $adminNavigation = $controller->adminNavigationWidget();

                if (null !== $adminNavigation) {
                    $controller->layout()->addChild($adminNavigation, 'adminNavigation');
                }
            },
            100
        );

        $eventManager->attach(
            MvcEvent::EVENT_RENDER_ERROR,
            [$this, 'error']
        );

        $this->initSearch();
    }

    /**
     * @todo should be in search module but used in DigitalGallery, Course etc aswell
     *
     * @return void
     */
    protected function initSearch()
    {
        SearchQueryParser::setDefaultEncoding('utf-8');
        SearchAnalyzer::setDefault(new SearchAnalyzerCaseInsensitive());
    }

    /**
     * @param MvcEvent $event
     * @return void
     */
    public function error(MvcEvent $event)
    {
        $logger = $event->getApplication()->getServiceManager()->get(Logger::class);

        $exception = $event->getParam('exception');

        if (null !== $exception) {
            $logger->err($exception);
        }
    }

}
