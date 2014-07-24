<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\BookInterface;
use N1c0\QuoteBundle\Model\BookManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wraps a real implementation of BookManagerInterface and
 * performs Acl checks with the configured Book Acl service.
 */
class AclBookManager implements BookManagerInterface
{
    /**
     * The BookManager instance to be wrapped with ACL.
     *
     * @var BookManagerInterface
     */
    protected $realManager;

    /**
     * The BookAcl instance for checking permissions.
     *
     * @var BookAclInterface
     */
    protected $bookAcl;

    /**
     * Constructor.
     *
     * @param BookManagerInterface $bookManager The concrete BookManager service
     * @param BookAclInterface     $bookAcl     The Book Acl service
     */
    public function __construct(BookManagerInterface $bookManager, BookAclInterface $bookAcl)
    {
        $this->realManager = $bookManager;
        $this->bookAcl  = $bookAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function all($limit = 5, $offset = 0)
    {
        $books = $this->realManager->all();

        if (!$this->authorizeViewBook($books)) {
            throw new AccessDeniedException();
        }

        return $books;
    }

    /**
     * {@inheritDoc}
     */
    public function findBookBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findBooksBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAllBooks(){
    }                 


    /**
     * {@inheritDoc}
     */
    public function saveBook(BookInterface $book)
    {
        if (!$this->bookAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        $newBook = $this->isNewBook($book);

        if (!$newBook && !$this->bookAcl->canEdit($book)) {
            throw new AccessDeniedException();
        }

        if (($book::STATE_DELETED === $book->getState() || $book::STATE_DELETED === $book->getPreviousState())
            && !$this->bookAcl->canDelete($book)
        ) {
            throw new AccessDeniedException();
        }

        $this->realManager->saveBook($book);

        if ($newBook) {
            $this->bookAcl->setDefaultAcl($book);
        }
    }

    /**
     * {@inheritDoc}
     **/
    public function findBookById($id)
    {
        $book = $this->realManager->findBookById($id);

        if (null !== $book && !$this->bookAcl->canView($book)) {
            throw new AccessDeniedException();
        }

        return $book;
    }

    /**
     * {@inheritDoc}
     */
    public function createBook($id = null)
    {
        return $this->realManager->createBook($id);
    }

    /**
     * {@inheritDoc}
     */
    public function isNewBook(BookInterface $book)
    {
        return $this->realManager->isNewBook($book);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }

    /**
     * Check if the book have appropriate view permissions.
     *
     * @param  array   $books A comment tree
     * @return boolean
     */
    protected function authorizeViewBook(array $books)
    {
        foreach ($books as $book) {
            if (!$this->bookAcl->canView($book)) {
                return false;
            }
        }

        return true;
    }
}
