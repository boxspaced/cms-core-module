<?php
namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Account\Service\AccountService;

/**
 * @todo move to a Zend\Navigation object
 */
class AdminNavigationWidget extends AbstractPlugin
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
     * @param bool $new
     * @return ViewModel|null
     */
    public function __invoke($new = false)
    {
        if (null === $this->accountService->getIdentity()) {
            return null;
        }

        $digitalGallery = $this->accountService->isAllowed('digital-gallery', 'manage');
        $course = $this->accountService->isAllowed('course', 'manage');
        $whatsOn = $this->accountService->isAllowed('whats-on', 'manage');

        $allowedModules = [];

        if ($digitalGallery) {
            $allowedModules[] = 'digital-gallery';
        }
        if ($course) {
            $allowedModules[] = 'course';
        }
        if ($whatsOn) {
            $allowedModules[] = 'whats-on';
        }

        $partial = 'admin-navigation-widget';
        if ($new) {
            $partial = 'new-admin-navigation-widget';
        }

        $viewModel = new ViewModel([
            'manageableModules' => $allowedModules,
            'allowViewAuthoring' => $this->accountService->isAllowed('workflow', 'authoring'),
            'allowViewPublishing' => $this->accountService->isAllowed('workflow', 'publishing'),
            'allowManageAssets' => $this->accountService->isAllowed('asset', 'index'),
        ]);

        return $viewModel->setTemplate('core/partial/' . $partial . '.phtml');
    }

}
