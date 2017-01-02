<?php
namespace Core\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Block\Model\Block;

class ModulePageFreeBlock extends AbstractEntity
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
     * @return ModulePageFreeBlock
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
        return $this->get('parentModulePage');
    }

    /**
     * @param ModulePage $parentModulePage
     * @return ModulePageFreeBlock
     */
    public function setParentModulePage(ModulePage $parentModulePage)
    {
        $this->set('parentModulePage', $parentModulePage);
		return $this;
    }

    /**
     * @return ModulePageBlock
     */
    public function getModulePageBlock()
    {
        return $this->get('modulePageBlock');
    }

    /**
     * @param ModulePageBlock $modulePageBlock
     * @return ModulePageFreeBlock
     */
    public function setModulePageBlock(ModulePageBlock $modulePageBlock)
    {
        $this->set('modulePageBlock', $modulePageBlock);
		return $this;
    }

    /**
     * @return Block
     */
    public function getBlock()
    {
        return $this->get('block');
    }

    /**
     * @param Block $block
     * @return ModulePageFreeBlock
     */
    public function setBlock($block)
    {
        $this->set('block', $block);
		return $this;
    }

}
