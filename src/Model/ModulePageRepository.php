<?php
namespace Boxspaced\CmsCoreModule\Model;

use Boxspaced\EntityManager\EntityManager;
use Boxspaced\EntityManager\Collection\Collection;

class ModulePageRepository
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
     * @return ModulePage
     */
    public function getById($id)
    {
        return $this->entityManager->find(ModulePage::class, $id);
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        return $this->entityManager->findAll(ModulePage::class);
    }

    /**
     * @param ModulePage $entity
     * @return ModulePageRepository
     */
    public function delete(ModulePage $entity)
    {
        $this->entityManager->delete($entity);
        return $this;
    }

}
