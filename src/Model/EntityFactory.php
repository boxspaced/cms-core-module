<?php
namespace Core\Model;

use Boxspaced\EntityManager\EntityManager;
use Boxspaced\EntityManager\Entity\AbstractEntity;

class EntityFactory
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $type
     * @param bool $persist
     * @return AbstractEntity
     */
    public function createEntity($type, $persist = true)
    {
        $entity = $this->entityManager->createEntity($type);

        if ($persist) {
            $this->entityManager->persist($entity);
        }

        return $entity;
    }

}
