<?php

namespace N1c0\QuoteBundle\Entity;

use Doctrine\ORM\EntityManager;
use N1c0\QuoteBundle\Model\AuthorSrcManager as BaseAuthorSrcManager;
use N1c0\QuoteBundle\Model\QuoteInterface;
use N1c0\QuoteBundle\Model\AuthorSrcInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM AuthorSrcManager.
 *
 */
class AuthorSrcManager extends BaseAuthorSrcManager
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
     * Returns a flat array of authorSrcs of a specific quote.
     *
     * @param  QuoteInterface $quote
     * @return array           of QuoteInterface
     */
    public function findAuthorSrcsByQuote(QuoteInterface $quote)
    {
        $qb = $this->repository
                ->createQueryBuilder('i')
                ->join('i.quote', 'd')
                ->where('d.id = :quote')
                ->setParameter('quote', $quote->getId());

        $authorSrcs = $qb
            ->getQuery()
            ->execute();

        return $authorSrcs;
    }

    /**
     * Find one authorSrc by its ID
     *
     * @param  array           $criteria
     * @return AuthorSrcInterface
     */
    public function findQuoteById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Finds all AuthorSrcs.
     *
     * @return array of AuthorSrcInterface
     */
    public function findAllAuthorSrcs()
    {
        return $this->repository->findAll();
    }

    /**
     * Performs persisting of the authorSrc. 
     *
     * @param QuoteInterface $quote
     */
    protected function doSaveAuthorSrc(AuthorSrcInterface $authorSrc)
    {
        $this->em->persist($authorSrc->getQuote());
        $this->em->persist($authorSrc);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified authorSrc quote class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
