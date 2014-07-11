<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\AuthorSrcEvent;
use N1c0\DissertationBundle\Model\SignedAuthorSrcInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a authorSrc using Symfony2 security component
 */
class AuthorSrcBlamerListener implements EventSubscriberInterface
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
     * Assigns the currently logged in user to a AuthorSrc.
     *
     * @param  \N1c0\DissertationBundle\Event\AuthorSrcEvent $event
     * @return void
     */
    public function blame(AuthorSrcEvent $event)
    {
        $authorSrc = $event->getAuthorSrc();

        if (null === $this->securityContext) {
            if ($this->logger) {
                $this->logger->debug("AuthorSrc Blamer did not receive the security.context service.");
            }

            return;
        }

        if (!$authorSrc instanceof SignedAuthorSrcInterface) {
            if ($this->logger) {
                $this->logger->debug("AuthorSrc does not implement SignedAuthorSrcInterface, skipping");
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
            $authorSrc->setAuthor($user);
            if (!$authorSrc->getAuthors()->contains($user)) {
                $authorSrc->addAuthor($user);
            }
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::AUTHORSRC_PRE_PERSIST => 'blame');
    }
}
