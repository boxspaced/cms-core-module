<?php
namespace Core\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;

class ProvisionalLocation extends AbstractEntity
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
     * @return ProvisionalLocation
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->get('to');
    }

    /**
     * @param string $to
     * @return ProvisionalLocation
     */
    public function setTo($to)
    {
        $this->set('to', $to);
		return $this;
    }

    /**
     * @return int
     */
    public function getBeneathMenuItemId()
    {
        return $this->get('beneathMenuItemId');
    }

    /**
     * @param int $beneathMenuItemId
     * @return ProvisionalLocation
     */
    public function setBeneathMenuItemId($beneathMenuItemId)
    {
        $this->set('beneathMenuItemId', $beneathMenuItemId);
		return $this;
    }

}
