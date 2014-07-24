<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\AuthorsrcInterface;
use N1c0\QuoteBundle\Model\AuthorsrcManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wraps a real implementation of AuthorsrcManagerInterface and
 * performs Acl checks with the configured Authorsrc Acl service.
 */
class AclAuthorsrcManager implements AuthorsrcManagerInterface
{
    /**
     * The AuthorsrcManager instance to be wrapped with ACL.
     *
     * @var AuthorsrcManagerInterface
     */
    protected $realManager;

    /**
     * The AuthorsrcAcl instance for checking permissions.
     *
     * @var AuthorsrcAclInterface
     */
    protected $authorsrcAcl;

    /**
     * Constructor.
     *
     * @param AuthorsrcManagerInterface $authorsrcManager The concrete AuthorsrcManager service
     * @param AuthorsrcAclInterface     $authorsrcAcl     The Authorsrc Acl service
     */
    public function __construct(AuthorsrcManagerInterface $authorsrcManager, AuthorsrcAclInterface $authorsrcAcl)
    {
        $this->realManager      = $authorsrcManager;
        $this->authorsrcAcl  = $authorsrcAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function all($limit = 5, $offset = 0)
    {
        $authorsrcs = $this->realManager->all();

        if (!$this->authorizeViewAuthorsrc($authorsrcs)) {
            throw new AccessDeniedException();
        }

        return $authorsrcs;
    }

    /**
     * {@inheritDoc}
     */
    public function findAuthorsrcBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAuthorsrcsBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAllAuthorsrcs(){
    }                 


    /**
     * {@inheritDoc}
     */
    public function saveAuthorsrc(AuthorsrcInterface $authorsrc)
    {
        if (!$this->authorsrcAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        $newAuthorsrc = $this->isNewAuthorsrc($authorsrc);

        if (!$newAuthorsrc && !$this->authorsrcAcl->canEdit($authorsrc)) {
            throw new AccessDeniedException();
        }

        if (($authorsrc::STATE_DELETED === $authorsrc->getState() || $authorsrc::STATE_DELETED === $authorsrc->getPreviousState())
            && !$this->authorsrcAcl->canDelete($authorsrc)
        ) {
            throw new AccessDeniedException();
        }

        $this->realManager->saveAuthorsrc($authorsrc);

        if ($newAuthorsrc) {
            $this->authorsrcAcl->setDefaultAcl($authorsrc);
        }
    }

    /**
     * {@inheritDoc}
     **/
    public function findAuthorsrcById($id)
    {
        $authorsrc = $this->realManager->findAuthorsrcById($id);

        if (null !== $authorsrc && !$this->authorsrcAcl->canView($authorsrc)) {
            throw new AccessDeniedException();
        }

        return $authorsrc;
    }

    /**
     * {@inheritDoc}
     */
    public function createAuthorsrc($id = null)
    {
        return $this->realManager->createAuthorsrc($id);
    }

    /**
     * {@inheritDoc}
     */
    public function isNewAuthorsrc(AuthorsrcInterface $authorsrc)
    {
        return $this->realManager->isNewAuthorsrc($authorsrc);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }

    /**
     * Check if the authorsrc have appropriate view permissions.
     *
     * @param  array   $authorsrcs A comment tree
     * @return boolean
     */
    protected function authorizeViewAuthorsrc(array $authorsrcs)
    {
        foreach ($authorsrcs as $authorsrc) {
            if (!$this->authorsrcAcl->canView($authorsrc)) {
                return false;
            }
        }

        return true;
    }
}
