<?php

namespace N1c0\DissertationBundle\Model;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\AuthorSrcEvent;
use N1c0\DissertationBundle\Event\AuthorSrcPersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use InvalidAuthorSrcException;

/**
 * Abstract AuthorSrc Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class AuthorSrcManager implements AuthorSrcManagerInterface
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
     * Get a list of AuthorSrcs.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0)
    {
        return $this->repository->findBy(array(), array('createdAt' => 'DESC'), $limit, $offset);
    }

    /**
     * @param  string          $id
     * @return AuthorSrcInterface
     */
    public function findAuthorSrcById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Returns an empty authorSrc instance
     *
     * @return AuthorSrc
     */
    public function createAuthorSrc(DissertationInterface $dissertation)
    {
        $class = $this->getClass();
        $authorSrc = new $class;

        $authorSrc->setDissertation($dissertation);

        $event = new AuthorSrcEvent($authorSrc);
        $this->dispatcher->dispatch(Events::AUTHORSRC_CREATE, $event);

        return $authorSrc;
    }

    /**
     * Saves a authorSrc to the persistence backend used. Each backend
     * must implement the abstract doSaveAuthorSrc method which will
     * perform the saving of the authorSrc to the backend.
     *
     * @param  AuthorSrcInterface         $authorSrc
     * @throws InvalidAuthorSrcException when the authorSrc does not have a dissertation.
     */
    public function saveAuthorSrc(AuthorSrcInterface $authorSrc)
    {
        if (null === $authorSrc->getDissertation()) {
            throw new InvalidAuthorSrcException('The authorSrc must have a dissertation');
        }

        $event = new AuthorSrcPersistEvent($authorSrc);
        $this->dispatcher->dispatch(Events::AUTHORSRC_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return false;
        }

        $this->doSaveAuthorSrc($authorSrc);

        $event = new AuthorSrcEvent($authorSrc);
        $this->dispatcher->dispatch(Events::AUTHORSRC_POST_PERSIST, $event);

        return true;
    }

    /**
     * Performs the persistence of a authorSrc.
     *
     * @abstract
     * @param AuthorSrcInterface $authorSrc
     */
    abstract protected function doSaveAuthorSrc(AuthorSrcInterface $authorSrc);
}
