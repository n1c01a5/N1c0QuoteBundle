<?php

namespace N1c0\QuoteBundle\EventListener;

use N1c0\QuoteBundle\Events;
use N1c0\QuoteBundle\Event\AuthorsrcEvent;
use N1c0\QuoteBundle\Model\SignedAuthorsrcInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a authorsrc using Symfony2 security component
 */
class AuthorsrcBlamerListener implements EventSubscriberInterface
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
     * Assigns the currently logged in user to a Authorsrc.
     *
     * @param  \N1c0\QuoteBundle\Event\AuthorsrcEvent $event
     * @return void
     */
    public function blame(AuthorsrcEvent $event)
    {
        $authorsrc = $event->getAuthorsrc();

        if (null === $this->securityContext) {
            if ($this->logger) {
                $this->logger->debug("Authorsrc Blamer did not receive the security.context service.");
            }

            return;
        }

        if (!$authorsrc instanceof SignedAuthorsrcInterface) {
            if ($this->logger) {
                $this->logger->debug("Authorsrc does not implement SignedAuthorsrcInterface, skipping");
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
            $authorsrc->setAuthor($user);
            if (!$authorsrc->getAuthors()->contains($user)) {
                $authorsrc->addAuthor($user);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::AUTHORSRC_PRE_PERSIST => 'blame');
    }
}
