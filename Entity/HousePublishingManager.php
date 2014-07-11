<?php

namespace N1c0\DissertationBundle\Entity;

use Doctrine\ORM\EntityManager;
use N1c0\DissertationBundle\Model\HousePublishingManager as BaseHousePublishingManager;
use N1c0\DissertationBundle\Model\DissertationInterface;
use N1c0\DissertationBundle\Model\HousePublishingInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM HousePublishingManager.
 *
 */
class HousePublishingManager extends BaseHousePublishingManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher 
     * @param \Doctrine\ORM\EntityManager                                 $em
     * @param string                                                      $class
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class)
    {
        parent::__construct($dispatcher);

        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * Returns a flat array of housePublishings of a specific dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return array           of DissertationInterface
     */
    public function findHousePublishingsByDissertation(DissertationInterface $dissertation)
    {
        $qb = $this->repository
                ->createQueryBuilder('i')
                ->join('i.dissertation', 'd')
                ->where('d.id = :dissertation')
                ->add('orderBy', 'p.createdAt DESC')
                ->setParameter('dissertation', $dissertation->getId());

        $housePublishings = $qb
            ->getQuery()
            ->execute();

        return $housePublishings;
    }

    /**
     * Find one housePublishing by its ID
     *
     * @param  array           $criteria
     * @return HousePublishingInterface
     */
    public function findDissertationById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Finds all HousePublishings.
     *
     * @return array of HousePublishingInterface
     */
    public function findAllHousePublishings()
    {
        return $this->repository->findAll();
    }

    /**
     * Performs persisting of the housePublishing. 
     *
     * @param DissertationInterface $dissertation
     */
    protected function doSaveHousePublishing(HousePublishingInterface $housePublishing)
    {
        $this->em->persist($housePublishing->getDissertation());
        $this->em->persist($housePublishing);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified housePublishing dissertation class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
