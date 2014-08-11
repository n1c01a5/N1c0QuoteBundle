<?php

namespace N1c0\QuoteBundle\Model;

use N1c0\QuoteBundle\Events;
use N1c0\QuoteBundle\Event\AuthorsrcEvent;
use N1c0\QuoteBundle\Event\AuthorsrcPersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use InvalidAuthorsrcException;

/**
 * Abstract Authorsrc Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class AuthorsrcManager implements AuthorsrcManagerInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Get a list of Authorsrcs.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0)
    {
        return $this->repository->findBy(array(), array(), $limit, $offset);
    }

    /**
     * @param  string          $id
     * @return AuthorsrcInterface
     */
    public function findAuthorsrcById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Returns an empty authorsrc instance
     *
     * @return Authorsrc
     */
    public function createAuthorsrc(QuoteInterface $quote)
    {
        $class = $this->getClass();
        $authorsrc = new $class;

        $authorsrc->setQuote($quote);

        $event = new AuthorsrcEvent($authorsrc);
        $this->dispatcher->dispatch(Events::AUTHORSRC_CREATE, $event);

        return $authorsrc;
    }

    /**
     * Saves a authorsrc to the persistence backend used. Each backend
     * must implement the abstract doSaveAuthorsrc method which will
     * perform the saving of the authorsrc to the backend.
     *
     * @param  AuthorsrcInterface         $authorsrc
     * @throws InvalidAuthorsrcException when the authorsrc does not have a quote.
     */
    public function saveAuthorsrc(AuthorsrcInterface $authorsrc)
    {
        $event = new AuthorsrcPersistEvent($authorsrc);
        $this->dispatcher->dispatch(Events::AUTHORSRC_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return false;
        }

        $this->doSaveAuthorsrc($authorsrc);

        $event = new AuthorsrcEvent($authorsrc);
        $this->dispatcher->dispatch(Events::AUTHORSRC_POST_PERSIST, $event);

        return true;
    }

    /**
     * Performs the persistence of a authorsrc.
     *
     * @abstract
     * @param AuthorsrcInterface $authorsrc
     */
    abstract protected function doSaveAuthorsrc(AuthorsrcInterface $authorsrc);
}
