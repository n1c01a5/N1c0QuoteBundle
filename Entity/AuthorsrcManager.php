<?php

namespace N1c0\QuoteBundle\Entity;

use Doctrine\ORM\EntityManager;
use N1c0\QuoteBundle\Model\AuthorsrcManager as BaseAuthorsrcManager;
use N1c0\QuoteBundle\Model\QuoteInterface;
use N1c0\QuoteBundle\Model\AuthorsrcInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM AuthorsrcManager.
 *
 */
class AuthorsrcManager extends BaseAuthorsrcManager
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
     * Returns a flat array of authorsrc of a specific quote.
     *
     * @param  QuoteInterface $quote
     * @return array           of QuoteInterface
     */
    public function findAuthorsrcByQuote(QuoteInterface $quote)
    {
        return $quote->getAuthorsrc();
    }

    /**
     * Find one authorsrc by its ID
     *
     * @param  array           $criteria
     * @return AuthorsrcInterface
     */
    public function findQuoteById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Finds all Authorsrcs.
     *
     * @return array of AuthorsrcInterface
     */
    public function findAllAuthorsrcs()
    {
        return $this->repository->findAll();
    }

    /**
     * Performs persisting of the authorsrc. 
     *
     * @param QuoteInterface $quote
     */
    protected function doSaveAuthorsrc(AuthorsrcInterface $authorsrc)
    {
        $this->em->persist($authorsrc);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified authorsrc quote class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
