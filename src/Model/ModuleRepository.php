<?php
namespace Boxspaced\CmsCoreModule\Model;

use Boxspaced\EntityManager\EntityManager;
use Boxspaced\EntityManager\Collection\Collection;

class ModuleRepository
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     * @return Module
     */
    public function getById($id)
    {
        return $this->entityManager->find(Module::class, $id);
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        return $this->entityManager->findAll(Module::class);
    }

    /**
     * @param string $name
     * @return Module
     */
    public function getByName($name)
    {
        $conditions = $this->entityManager->createConditions();
        $conditions->field('name')->eq($name);
        return $this->entityManager->findOne(Module::class, $conditions);
    }

    /**
     * @param Module $entity
     * @return ModuleRepository
     */
    public function delete(Module $entity)
    {
        $this->entityManager->delete($entity);
        return $this;
    }

}
