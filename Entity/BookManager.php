<?php

namespace N1c0\QuoteBundle\Entity;

use Doctrine\ORM\EntityManager;
use N1c0\QuoteBundle\Model\BookManager as BaseBookManager;
use N1c0\QuoteBundle\Model\QuoteInterface;
use N1c0\QuoteBundle\Model\BookInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM BookManager.
 *
 */
class BookManager extends BaseBookManager
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
     * Returns a flat array of books of a specific quote.
     *
     * @param  QuoteInterface $quote
     * @return array           of QuoteInterface
     */
    public function findBooksByQuote(QuoteInterface $quote)
    {
        $qb = $this->repository
                ->createQueryBuilder('a')
                ->join('a.quote', 'd')
                ->where('d.id = :quote')
                ->setParameter('quote', $quote->getId());

        $books = $qb
            ->getQuery()
            ->execute();

        return $books;
    }

    /**
     * Find one book by its ID
     *
     * @param  array           $criteria
     * @return BookInterface
     */
    public function findQuoteById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Finds all Books.
     *
     * @return array of BookInterface
     */
    public function findAllBooks()
    {
        return $this->repository->findAll();
    }

    /**
     *
     * {@inheritDoc}
     *
    */
    public function isNewBook(BookInterface $book)
    {
        return !$this->em->getUnitOfWork()->isInIdentityMap($book);
    }

    /**
     * Performs persisting of the book.
     *
     * @param QuoteInterface $quote
     */
    protected function doSaveBook(BookInterface $book)
    {
        $book->addQuote($book->getQuote());
        $this->em->persist($book);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified book quote class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
