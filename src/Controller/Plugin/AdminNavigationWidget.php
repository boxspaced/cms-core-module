<?php
namespace Boxspaced\CmsCoreModule\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Boxspaced\CmsAccountModule\Service\AccountService;
use Boxspaced\CmsDigitalGalleryModule\Controller\DigitalGalleryController;
use Boxspaced\CmsCourseModule\Controller\CourseController;
use Boxspaced\CmsWhatsOnModule\Controller\WhatsOnController;

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

        // @todo should be testing if module is installed
        $digitalGallery = class_exists(DigitalGalleryController::class) && $this->accountService->isAllowed(DigitalGalleryController::class, 'manage');
        $course = class_exists(CourseController::class) && $this->accountService->isAllowed(CourseController::class, 'manage');
        $whatsOn = class_exists(WhatsOnController::class) && $this->accountService->isAllowed(WhatsOnController::class, 'manage');

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

        return $viewModel->setTemplate('boxspaced/cms-core-module/partial/' . $partial . '.phtml');
    }

}
