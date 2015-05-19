<?php

namespace N1c0\QuoteBundle\EventListener;

use N1c0\QuoteBundle\Events;
use N1c0\QuoteBundle\Event\BookEvent;
use N1c0\QuoteBundle\Model\SignedBookInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a book using Symfony2 security component
 */
class BookBlamerListener implements EventSubscriberInterface
{
    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface $securityContext
     * @param LoggerInterface          $logger
     */
    public function __construct(SecurityContextInterface $securityContext = null, LoggerInterface $logger = null)
    {
        $this->securityContext = $securityContext;
        $this->logger = $logger;
    }

    /**
     * Assigns the currently logged in user to a Book.
     *
     * @param  \N1c0\QuoteBundle\Event\BookEvent $event
     * @return void
     */
    public function blame(BookEvent $event)
    {
        $book = $event->getBook();

        if (null === $this->securityContext) {
            if ($this->logger) {
                $this->logger->debug("Book Blamer did not receive the security.context service.");
            }

            return;
        }

        if (!$book instanceof SignedBookInterface) {
            if ($this->logger) {
                $this->logger->debug("Book does not implement SignedBookInterface, skipping");
            }

            return;
        }

        if (null === $this->securityContext->getToken()) {
            if ($this->logger) {
                $this->logger->debug("There is no firewall configured. We cant get a user.");
            }

            return;
        }

        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->securityContext->getToken()->getUser();
            $book->setAuthor($user);
            if (!$book->getAuthors()->contains($user)) {
                $book->addAuthor($user);
            }
            if (!$book->getQuote()->getAuthors()->contains($user)) {
                $book->getQuote()->addAuthor($user);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::BOOK_PRE_PERSIST => 'blame');
    }
}
