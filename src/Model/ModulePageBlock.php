<?php
namespace Boxspaced\CmsCoreModule\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;

class ModulePageBlock extends AbstractEntity
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
     * @return ModulePageBlock
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
     * @return ModulePageBlock
     */
    public function setParentModulePage(ModulePage $parentModulePage)
    {
        $this->set('parent_module_page', $parentModulePage);
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
     * @return ModulePageBlock
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return string
     */
    public function getAdminLabel()
    {
        return $this->get('admin_label');
    }

    /**
     * @param string $adminLabel
     * @return ModulePageBlock
     */
    public function setAdminLabel($adminLabel)
    {
        $this->set('admin_label', $adminLabel);
		return $this;
    }

    /**
     * @return bool
     */
    public function getSequence()
    {
        return $this->get('sequence');
    }

    /**
     * @param bool $sequence
     * @return ModulePageBlock
     */
    public function setSequence($sequence)
    {
        $this->set('sequence', $sequence);
		return $this;
    }

}
