<?php
namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Core\Service;

class ModulePageBlocks extends AbstractPlugin
{

    /**
     * @param ViewModel $parentViewModel
     * @param Service\ModulePagePublishingOptions $publishingOptions
     * @return ViewModel
     */
    public function __invoke(
        ViewModel $parentViewModel,
        Service\ModulePagePublishingOptions $publishingOptions
    )
    {
        return $this->assign($parentViewModel, $publishingOptions);
    }

    /**
     * @param ViewModel $parentViewModel
     * @param Service\ModulePagePublishingOptions $publishingOptions
     * @return ViewModel
     */
    public function assign(
        ViewModel $parentViewModel,
        Service\ModulePagePublishingOptions $publishingOptions
    )
    {
        foreach ($publishingOptions->freeBlocks as $freeBlock) {

            $block = $this->getController()->blockWidget(
                $freeBlock->id,
                $freeBlock->name
            );

            if (null === $block) {
                continue;
            }

            $parentViewModel->addChild($block, $freeBlock->name);
        }

        foreach ($publishingOptions->blockSequences as $blockSequence) {

            foreach ($blockSequence->blocks as $block) {

                $block = $this->getController()->blockWidget(
                    $block->id,
                    $blockSequence->name
                );

                if (null === $block) {
                    continue;
                }

                $parentViewModel->addChild($block, $blockSequence->name, true);
            }
        }

        return $parentViewModel;
    }

}
