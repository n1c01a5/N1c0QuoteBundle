<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\AuthorSrcInterface;
use N1c0\DissertationBundle\Model\AuthorSrcManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wraps a real implementation of AuthorSrcManagerInterface and
 * performs Acl checks with the configured AuthorSrc Acl service.
 */
class AclAuthorSrcManager implements AuthorSrcManagerInterface
{
    /**
     * The AuthorSrcManager instance to be wrapped with ACL.
     *
     * @var AuthorSrcManagerInterface
     */
    protected $realManager;

    /**
     * The AuthorSrcAcl instance for checking permissions.
     *
     * @var AuthorSrcAclInterface
     */
    protected $authorSrcAcl;

    /**
     * Constructor.
     *
     * @param AuthorSrcManagerInterface $authorSrcManager The concrete AuthorSrcManager service
     * @param AuthorSrcAclInterface     $authorSrcAcl     The AuthorSrc Acl service
     */
    public function __construct(AuthorSrcManagerInterface $authorSrcManager, AuthorSrcAclInterface $authorSrcAcl)
    {
        $this->realManager      = $authorSrcManager;
        $this->authorSrcAcl  = $authorSrcAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function all($limit = 5, $offset = 0)
    {
        $authorSrcs = $this->realManager->all();

        if (!$this->authorizeViewAuthorSrc($authorSrcs)) {
            throw new AccessDeniedException();
        }

        return $authorSrcs;
    }

    /**
     * {@inheritDoc}
     */
    public function findAuthorSrcBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAuthorSrcsBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAllAuthorSrcs(){
    }                 


    /**
     * {@inheritDoc}
     */
    public function saveAuthorSrc(AuthorSrcInterface $authorSrc)
    {
        if (!$this->authorSrcAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        $newAuthorSrc = $this->isNewAuthorSrc($authorSrc);

        if (!$newAuthorSrc && !$this->authorSrcAcl->canEdit($authorSrc)) {
            throw new AccessDeniedException();
        }

        if (($authorSrc::STATE_DELETED === $authorSrc->getState() || $authorSrc::STATE_DELETED === $authorSrc->getPreviousState())
            && !$this->authorSrcAcl->canDelete($authorSrc)
        ) {
            throw new AccessDeniedException();
        }

        $this->realManager->saveAuthorSrc($authorSrc);

        if ($newAuthorSrc) {
            $this->authorSrcAcl->setDefaultAcl($authorSrc);
        }
    }

    /**
     * {@inheritDoc}
     **/
    public function findAuthorSrcById($id)
    {
        $authorSrc = $this->realManager->findAuthorSrcById($id);

        if (null !== $authorSrc && !$this->authorSrcAcl->canView($authorSrc)) {
            throw new AccessDeniedException();
        }

        return $authorSrc;
    }

    /**
     * {@inheritDoc}
     */
    public function createAuthorSrc($id = null)
    {
        return $this->realManager->createAuthorSrc($id);
    }

    /**
     * {@inheritDoc}
     */
    public function isNewAuthorSrc(AuthorSrcInterface $authorSrc)
    {
        return $this->realManager->isNewAuthorSrc($authorSrc);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }

    /**
     * Check if the authorSrc have appropriate view permissions.
     *
     * @param  array   $authorSrcs A comment tree
     * @return boolean
     */
    protected function authorizeViewAuthorSrc(array $authorSrcs)
    {
        foreach ($authorSrcs as $authorSrc) {
            if (!$this->authorSrcAcl->canView($authorSrc)) {
                return false;
            }
        }

        return true;
    }
}
