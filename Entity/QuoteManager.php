<?php

namespace N1c0\QuoteBundle\Entity;

use Doctrine\ORM\EntityManager;
use N1c0\QuoteBundle\Model\QuoteManager as BaseQuoteManager;
use N1c0\QuoteBundle\Model\QuoteInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM QuoteManager.
 *
 */
class QuoteManager extends BaseQuoteManager
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
     * Finds one element quote by the given criteria
     *
     * @param  array           $criteria
     * @return QuoteInterface
     */
    public function findQuoteBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findQuotesBy(array $criteria, $limit = 5,  $offset = 0)
    {
        return $this->repository->findBy(array('bodsy'=>'lmk'), null );
    }

    /**
     * Finds all quotes.
     *
     * @return array of QuoteInterface
     */
    public function findAllQuotes()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function isNewQuote(QuoteInterface $quote)
    {
        return !$this->em->getUnitOfWork()->isInIdentityMap($quote);
    }

    /**
     * Saves a quote
     *
     * @param QuoteInterface $quote
     */
    protected function doSaveQuote(QuoteInterface $quote)
    {
        $this->em->persist($quote);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified element quote class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
