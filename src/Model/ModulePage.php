<?php
namespace Core\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\EntityManager\Collection\Collection;

class ModulePage extends AbstractEntity
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
     * @return ModulePage
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return Module
     */
    public function getParentModule()
    {
        return $this->get('parentModule');
    }

    /**
     * @param Module $parentModule
     * @return ModulePage
     */
    public function setParentModule(Module $parentModule)
    {
        $this->set('parentModule', $parentModule);
		return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * @param string $name
     * @return ModulePage
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return Collection
     */
    public function getFreeBlocks()
    {
        return $this->get('freeBlocks');
    }

    /**
     * @param ModulePageFreeBlock $freeBlock
     * @return ModulePage
     */
    public function addFreeBlock(ModulePageFreeBlock $freeBlock)
    {
        $freeBlock->setParentModulePage($this);
        $this->getFreeBlocks()->add($freeBlock);
		return $this;
    }

    /**
     * @param ModulePageFreeBlock $freeBlock
     * @return ModulePage
     */
    public function deleteFreeBlock(ModulePageFreeBlock $freeBlock)
    {
        $this->getFreeBlocks()->delete($freeBlock);
		return $this;
    }

    /**
     * @return ModulePage
     */
    public function deleteAllFreeBlocks()
    {
        foreach ($this->getFreeBlocks() as $freeBlock) {
            $this->deleteFreeBlock($freeBlock);
        }
		return $this;
    }

    /**
     * @return Collection
     */
    public function getBlockSequences()
    {
        return $this->get('blockSequences');
    }

    /**
     * @param ModulePageBlockSequence $blockSequence
     * @return ModulePage
     */
    public function addBlockSequence(ModulePageBlockSequence $blockSequence)
    {
        $blockSequence->setParentModulePage($this);
        $this->getBlockSequences()->add($blockSequence);
		return $this;
    }

    /**
     * @param ModulePageBlockSequence $blockSequence
     * @return ModulePage
     */
    public function deleteBlockSequence(ModulePageBlockSequence $blockSequence)
    {
        $this->getBlockSequences()->delete($blockSequence);
		return $this;
    }

    /**
     * @return ModulePage
     */
    public function deleteAllBlockSequences()
    {
        foreach ($this->getBlockSequences() as $blockSequence) {
            $this->deleteBlockSequence($blockSequence);
        }
		return $this;
    }

    /**
     * @return Collection
     */
    public function getBlocks()
    {
        return $this->get('blocks');
    }

    /**
     * @param ModulePageBlock $block
     * @return ModulePage
     */
    public function addPageBlock(ModulePageBlock $block)
    {
        $block->setParentModulePage($this);
        $this->getBlocks()->add($block);
		return $this;
    }

    /**
     * @param ModulePageBlock $block
     * @return ModulePage
     */
    public function deleteBlock(ModulePageBlock $block)
    {
        $this->getBlocks()->delete($block);
		return $this;
    }

    /**
     * @return ModulePage
     */
    public function deleteAllBlocks()
    {
        foreach ($this->getBlocks() as $block) {
            $this->deleteBlock($block);
        }
		return $this;
    }

}
