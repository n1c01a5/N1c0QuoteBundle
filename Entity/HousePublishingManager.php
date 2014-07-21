<?php

namespace N1c0\QuoteBundle\Entity;

use Doctrine\ORM\EntityManager;
use N1c0\QuoteBundle\Model\HousePublishingManager as BaseHousePublishingManager;
use N1c0\QuoteBundle\Model\QuoteInterface;
use N1c0\QuoteBundle\Model\HousePublishingInterface;
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
     * Returns a flat array of housePublishings of a specific quote.
     *
     * @param  QuoteInterface $quote
     * @return array           of QuoteInterface
     */
    public function findHousePublishingsByQuote(QuoteInterface $quote)
    {
        $qb = $this->repository
                ->createQueryBuilder('i')
                ->join('i.quote', 'd')
                ->where('d.id = :quote')
                ->setParameter('quote', $quote->getId());

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
    public function findQuoteById($id)
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
     * @param QuoteInterface $quote
     */
    protected function doSaveHousePublishing(HousePublishingInterface $housePublishing)
    {
        $this->em->persist($housePublishing->getQuote());
        $this->em->persist($housePublishing);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified housePublishing quote class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
