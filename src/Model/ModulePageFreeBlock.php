<?php
namespace Boxspaced\CmsCoreModule\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\CmsBlockModule\Model\Block;

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
        return $this->get('parent_module_page');
    }

    /**
     * @param ModulePage $parentModulePage
     * @return ModulePageFreeBlock
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
     * @return ModulePageFreeBlock
     */
    public function setModulePageBlock(ModulePageBlock $modulePageBlock)
    {
        $this->set('module_page_block', $modulePageBlock);
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
