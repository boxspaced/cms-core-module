<?php
namespace Core\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Block\Model\Block;

class ModulePageBlockSequenceBlock extends AbstractEntity
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
     * @return ModulePageBlockSequenceBlock
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return ModulePageBlockSequence
     */
    public function getParentBlockSequence()
    {
        return $this->get('parentBlockSequence');
    }

    /**
     * @param ModulePageBlockSequence $parentBlockSequence
     * @return ModulePageBlockSequenceBlock
     */
    public function setParentBlockSequence(ModulePageBlockSequence $parentBlockSequence)
    {
        $this->set('parentBlockSequence', $parentBlockSequence);
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
     * @return ModulePageBlockSequenceBlock
     */
    public function setBlock(Block $block)
    {
        $this->set('block', $block);
		return $this;
    }

    /**
     * @return int
     */
    public function getOrderBy()
    {
        return $this->get('orderBy');
    }

    /**
     * @param int $orderBy
     * @return ModulePageBlockSequenceBlock
     */
    public function setOrderBy($orderBy)
    {
        $this->set('orderBy', $orderBy);
		return $this;
    }

}
