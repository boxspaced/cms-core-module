<?php
namespace Boxspaced\CmsCoreModule\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Boxspaced\CmsAccountModule\Service\AccountService;

class ModulePageAdminWidget extends AbstractPlugin
{

    /**
     * @var AccountService
     */
    protected $accountService;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param AccountService $accountService
     * @param array $config
     */
    public function __construct(
        AccountService $accountService,
        array $config
    )
    {
        $this->accountService = $accountService;
        $this->config = $config;
    }

    /**
     * @todo just pass module page identifier in here and fetch data needed from services
     * @param string $moduleName
     * @param string $pageName
     * @param int $id
     * @param bool $hasBlocks
     * @return ViewModel|null
     */
    public function __invoke($moduleName, $pageName, $id, $hasBlocks = false)
    {
        if (null === $this->accountService->getIdentity()) {
            return null;
        }

        $canPublish = $this->accountService->isAllowed($moduleName, 'publish');

        $viewModel = new ViewModel([
            'id' => $id,
            'moduleName' => $moduleName,
            'pageName' => $pageName,
            'allowPublish' => ($hasBlocks && $canPublish),
        ]);

        return $viewModel->setTemplate('boxspaced/cms-core-module/partial/module-page-admin-widget.phtml');
    }

}
