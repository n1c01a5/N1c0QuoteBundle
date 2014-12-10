<?php

namespace N1c0\QuoteBundle\Entity;

use Doctrine\ORM\EntityManager;
use N1c0\QuoteBundle\Model\HousepublishingManager as BaseHousepublishingManager;
use N1c0\QuoteBundle\Model\QuoteInterface;
use N1c0\QuoteBundle\Model\HousepublishingInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM HousepublishingManager.
 *
 */
class HousepublishingManager extends BaseHousepublishingManager
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
     * Returns a flat array of housepublishings of a specific quote.
     *
     * @param  QuoteInterface $quote
     * @return array           of QuoteInterface
     */
    public function findHousepublishingsByQuote(QuoteInterface $quote)
    {
        $qb = $this->repository
                ->createQueryBuilder('i')
                ->join('i.quote', 'd')
                ->where('d.id = :quote')
                ->setParameter('quote', $quote->getId());

        $housepublishings = $qb
            ->getQuery()
            ->execute();

        return $housepublishings;
    }

    /**
     * Find one housepublishing by its ID
     *
     * @param  array           $criteria
     * @return HousepublishingInterface
     */
    public function findQuoteById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Finds all Housepublishings.
     *
     * @return array of HousepublishingInterface
     */
    public function findAllHousepublishings()
    {
        return $this->repository->findAll();
    }

    /**
     *
     * {@inheritDoc}
     *
    */
    public function isNewHousepublishing(HousepublishingInterface $housepublishing)
    {
        return !$this->em->getUnitOfWork()->isInIdentityMap($housepublishing);
    }

    /**
     * Performs persisting of the housepublishing.
     *
     * @param QuoteInterface $quote
     */
    protected function doSaveHousepublishing(HousepublishingInterface $housepublishing)
    {
        $this->em->persist($housepublishing->getQuote());
        $this->em->persist($housepublishing);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified housepublishing quote class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
