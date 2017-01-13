<?php
namespace Boxspaced\CmsCoreModule;

use Zend\Mvc\MvcEvent;
use Zend_Search_Lucene_Search_QueryParser as SearchQueryParser;
use Zend_Search_Lucene_Analysis_Analyzer as SearchAnalyzer;
use Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive as SearchAnalyzerCaseInsensitive;
use Zend\Log\Logger;

class Module
{

    const VERSION = '1.0.0';

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

        $eventManager->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this, 'error']
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

        $logger->err($exception ?: 'Error event caught but no exception available');
    }

}
