<?php
namespace Core\Service;

use Zend\Cache\Storage\Adapter\AbstractAdapter as Cache;
use Zend\Log\Logger;
use Zend\Authentication\AuthenticationService;
use Boxspaced\EntityManager\EntityManager;
use Core\Model;
use Core\Exception;
use Account\Model\UserRepository;
use Block\Model\BlockRepository;
use Core\Model\EntityFactory;
use Account\Model\User;
use Versioning\Model\VersionableInterface;

/**
 * @todo Rename as Module service and have module related operations
 */
class ModulePageService
{

    const CURRENT_PUBLISHING_OPTIONS_CACHE_ID = 'currentPublishingOptionsModulePage%d';

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var Model\ModulePageRepository
     */
    protected $modulePageRepository;

    /**
     * @var BlockRepository
     */
    protected $blockRepository;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @param Cache $cache
     * @param Logger $logger
     * @param AuthenticationService $authService
     * @param EntityManager $entityManager
     * @param UserRepository $userRepository
     * @param Model\ModulePageRepository $modulePageRepository
     * @param BlockRepository $blockRepository
     * @param EntityFactory $entityFactory
     */
    public function __construct(
        Cache $cache,
        Logger $logger,
        AuthenticationService $authService,
        EntityManager $entityManager,
        UserRepository $userRepository,
        Model\ModulePageRepository $modulePageRepository,
        BlockRepository $blockRepository,
        EntityFactory $entityFactory
    )
    {
        $this->cache = $cache;
        $this->logger = $logger;
        $this->authService = $authService;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->modulePageRepository = $modulePageRepository;
        $this->blockRepository = $blockRepository;
        $this->entityFactory = $entityFactory;

        if ($this->authService->hasIdentity()) {
            $identity = $authService->getIdentity();
            $this->user = $userRepository->getById($identity->id);
        }
    }

    /**
     * @param int $id
     * @return ModulePage
     */
    public function getModulePage($id)
    {
        $modulePageEntity = $this->modulePageRepository->getById($id);

        if (null === $modulePageEntity) {
            throw new Exception\UnexpectedValueException('Unable to find a module page with given ID');
        }

        $modulePage = new ModulePage();
        $modulePage->id = $modulePageEntity->getId();
        $modulePage->name = $modulePageEntity->getName();

        return $modulePage;
    }

    /**
     * @param int $id
     * @return ModulePageBlock[]
     */
    public function getModulePageBlocks($id)
    {
        $page = $this->modulePageRepository->getById($id);

        if (null === $page) {
            throw new Exception\UnexpectedValueException('Unable to find a module page with given ID');
        }

        $blocks = [];

        foreach ($page->getBlocks() as $blockEntity) {

            $block = new ModulePageBlock();
            $block->name = $blockEntity->getName();
            $block->adminLabel = $blockEntity->getAdminLabel();
            $block->sequence = (bool) $blockEntity->getSequence();

            $blocks[] = $block;
        }

        return $blocks;
    }

    /**
     * @param int $id
     * @return ModulePagePublishingOptions
     */
    public function getCurrentPublishingOptions($id)
    {
        $cacheId = sprintf(static::CURRENT_PUBLISHING_OPTIONS_CACHE_ID, $id);
        $cached = $this->cache->getItem($cacheId);

        if (null !== $cached) {
            return $cached;
        }

        $modulePageEntity = $this->modulePageRepository->getById($id);

        if (null === $modulePageEntity) {
            throw new Exception\UnexpectedValueException('Unable to find a module page with given ID');
        }

        $publishingOptions = new ModulePagePublishingOptions();

        foreach ($modulePageEntity->getFreeBlocks() as $freeBlockEntity) {

            if ($freeBlockEntity->getBlock()->getStatus() !== VersionableInterface::STATUS_PUBLISHED) {
                continue;
            }

            $freeBlock = new FreeBlock();
            $freeBlock->name = $freeBlockEntity->getModulePageBlock()->getName();
            $freeBlock->id = $freeBlockEntity->getBlock()->getId();

            $publishingOptions->freeBlocks[] = $freeBlock;
        }

        foreach ($modulePageEntity->getBlockSequences() as $blockSequenceEntity) {

            $blockSequence = new BlockSequence();
            $blockSequence->name = $blockSequenceEntity->getModulePageBlock()->getName();

            foreach ($blockSequenceEntity->getBlocks() as $blockSequenceBlockEntity) {

                if ($blockSequenceBlockEntity->getBlock()->getStatus() !== VersionableInterface::STATUS_PUBLISHED) {
                    continue;
                }

                $blockSequenceBlock = new BlockSequenceBlock();
                $blockSequenceBlock->id = $blockSequenceBlockEntity->getBlock()->getId();
                $blockSequenceBlock->orderBy = $blockSequenceBlockEntity->getOrderBy();

                $blockSequence->blocks[] = $blockSequenceBlock;
            }

            $publishingOptions->blockSequences[] = $blockSequence;
        }

        $this->cache->setItem($cacheId, $publishingOptions);

        return $publishingOptions;
    }

    /**
     * @param int $id
     * @param ModulePagePublishingOptions $options
     * @return void
     */
    public function publish($id, ModulePagePublishingOptions $options = null)
    {
        $page = $this->modulePageRepository->getById($id);

        if (null === $page) {
            throw new Exception\UnexpectedValueException('Unable to find module page');
        }

        // Remove all blocks
        $page->deleteAllFreeBlocks();
        $page->deleteAllBlockSequences();

        // Free blocks
        foreach ($options->freeBlocks as $freeBlock) {

            $pageBlocks = $page->getBlocks()->filter(function($pageBlock) use ($freeBlock) {
                return $pageBlock->getName() === $freeBlock->name;
            });
            $pageBlock = $pageBlocks->first();

            $block = $this->blockRepository->getById($freeBlock->id);

            if (null === $pageBlock || null === $block) {
                $this->logger->warn('Ignoring block');
                continue;
            }

            $pageFreeBlock = $this->entityFactory->createEntity(Model\ModulePageFreeBlock::class);
            $pageFreeBlock->setModulePageBlock($pageBlock);
            $pageFreeBlock->setBlock($block);

            $page->addFreeBlock($pageFreeBlock);
        }

        // Block sequences
        foreach ($options->blockSequences as $blockSequence) {

            if (!$blockSequence->blocks) {
                continue;
            }

            $pageBlocks = $page->getBlocks()->filter(function($pageBlock) use ($blockSequence) {
                return $pageBlock->getName() === $blockSequence->name;
            });
            $pageBlock = $pageBlocks->first();

            if (null === $pageBlock) {
                $this->logger->warn('Ignoring block sequence');
                continue;
            }

            $pageBlockSequence = $this->entityFactory->createEntity(Model\ModulePageBlockSequence::class);
            $pageBlockSequence->setModulePageBlock($pageBlock);

            // Blocks
            foreach ($blockSequence->blocks as $blockSequenceBlock) {

                $block = $this->blockRepository->getById($blockSequenceBlock->id);

                if (null === $block) {
                    $this->logger->warn('Ignoring block sequence block');
                    continue;
                }

                $pageBlockSequenceBlock = $this->entityFactory->createEntity(Model\ModulePageBlockSequenceBlock::class);
                $pageBlockSequenceBlock->setOrderBy($blockSequenceBlock->orderBy);
                $pageBlockSequenceBlock->setBlock($block);

                $pageBlockSequence->addBlock($pageBlockSequenceBlock);
            }

            $page->addBlockSequence($pageBlockSequence);
        }

        $this->entityManager->flush();

        // Clear cache
        $this->cache->removeItem(sprintf(static::CURRENT_PUBLISHING_OPTIONS_CACHE_ID, $id));
    }

}
