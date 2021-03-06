<?php

namespace Pok\PoolDBM;

use Pok\PoolDBM\Persisters\ModelBuilder;

/**
 * Model repository retrieve repository per manager type, and integrates tools for hydrate result.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class ModelRepository
{
    /**
     * @var ModelManager
     */
    protected $manager;

    /**
     * @var UnitOfWork
     */
    protected $uow;

    /**
     * @var Mapping\ClassMetadata
     */
    protected $class;

    /**
     * @var ModelBuilder
     */
    protected $modelBuilder;

    /**
     * Construtor.
     *
     * @param ModelManager          $manager
     * @param UnitOfWork            $uow
     * @param Mapping\ClassMetadata $class
     */
    public function __construct(ModelManager $manager, UnitOfWork $uow, Mapping\ClassMetadata $class)
    {
        $this->manager = $manager;
        $this->uow     = $uow;
        $this->class   = $class;

        $this->modelBuilder = new ModelBuilder($manager, $uow, $class);
    }

    public function createQueryBuilder($alias)
    {
        return $this->manager->createQueryBuilder($this->getClassName(), $alias);
    }

    /**
     * {@inheritDoc}
     */
    public function find($id)
    {
        return $this->uow->getModelPersister($this->getClassName())->load($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findAll()
    {
        return $this->findBy(array());
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->uow->getModelPersister($this->getClassName())->loadAll($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria)
    {
        return $this->find($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function getClassName()
    {
        return $this->class->getName();
    }

    /**
     * Multiple hydration model.
     *
     * @param array $objects
     * @param array $fields  List of fields prime (optional)
     *
     * @return array
     */
    public function hydrate(array $objects, array $fields = array())
    {
        return $this->modelBuilder->buildAll($objects, $this->class->getManagerIdentifier(), $fields);
    }

    /**
     * Return the result for given query builder object.
     *
     * @param mixed        $qb      Query or QueryBuilder object
     * @param integer|null $count   Number of items to retrieve (optional)
     * @param boolean      $hydrate Multi hydratation model (optional)
     * @param array        $fields  List of fields prime (optional)
     * @param boolean      $except  Keep object and ignore field adding in select query (optional)
     *
     * @return mixed
     */
    protected function getQueryBuilderResult($qb, $count = null, $hydrate = true, array $fields = array(), $except = false)
    {
        $result = $qb->execute();

        if ($except) {
            foreach ($result as $key => $value) {
                foreach ($value as $field => $data) {
                    if (is_int($field)) {
                        $result[$key] = $data;

                        continue 2;
                    }
                }
            }
        }

        if ($hydrate) {
            $result = $this->hydrate((array) $result, $fields);
        }

        if ($count === 1) {
            $result = is_array($result) ? reset($result) : $result;
        }

        return $result;
    }

    /**
     * Returns the result for given query builder object.
     *
     * @param mixed $qb Query builder object
     *
     * @return mixed
     */
    protected function getQueryBuilderOneOrNullResult($qb)
    {
        return $this->getQueryBuilderResult($qb, 1);
    }
}
