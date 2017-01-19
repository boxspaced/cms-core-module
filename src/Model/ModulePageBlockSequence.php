<?php
namespace Boxspaced\CmsCoreModule\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\EntityManager\Collection\Collection;

class ModulePageBlockSequence extends AbstractEntity
{

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     * @return ModulePageBlockSequence
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return ModulePage
     */
    public function getParentModulePage()
    {
        return $this->get('parent_module_page');
    }

    /**
     * @param ModulePage $parentModulePage
     * @return ModulePageBlockSequence
     */
    public function setParentModulePage(ModulePage $parentModulePage)
    {
        $this->set('parent_module_page', $parentModulePage);
		return $this;
    }

    /**
     * @return ModulePageBlock
     */
    public function getModulePageBlock()
    {
        return $this->get('module_page_block');
    }

    /**
     * @param ModulePageBlock $modulePageBlock
     * @return ModulePageBlockSequence
     */
    public function setModulePageBlock(ModulePageBlock $modulePageBlock)
    {
        $this->set('module_page_block', $modulePageBlock);
		return $this;
    }

    /**
     * @return Collection
     */
    public function getBlocks()
    {
        return $this->get('blocks')->sort(function(
            ModulePageBlockSequenceBlock $a,
            ModulePageBlockSequenceBlock $b
        ) {
            return $a->getOrderBy() - $b->getOrderBy();
        });
    }

    /**
     * @param ModulePageBlockSequenceBlock $block
     * @return ModulePageBlockSequence
     */
    public function addBlock(ModulePageBlockSequenceBlock $block)
    {
        $block->setParentBlockSequence($this);
        $this->getBlocks()->add($block);
		return $this;
    }

    /**
     * @param ModulePageBlockSequenceBlock $block
     * @return ModulePageBlockSequence
     */
    public function deleteBlock(ModulePageBlockSequenceBlock $block)
    {
        $this->getBlocks()->delete($block);
		return $this;
    }

    /**
     * @return ModulePageBlockSequence
     */
    public function deleteAllBlocks()
    {
        foreach ($this->getBlocks() as $block) {
            $this->deleteBlock($block);
        }
		return $this;
    }

}
