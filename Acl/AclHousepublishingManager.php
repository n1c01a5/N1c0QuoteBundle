<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\QuoteInterface;
use N1c0\QuoteBundle\Model\HousepublishingInterface;
use N1c0\QuoteBundle\Model\HousepublishingManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wraps a real implementation of HousepublishingManagerInterface and
 * performs Acl checks with the configured Housepublishing Acl service.
 */
class AclHousepublishingManager implements HousepublishingManagerInterface
{
    /**
     * The HousepublishingManager instance to be wrapped with ACL.
     *
     * @var HousepublishingManagerInterface
     */
    protected $realManager;

    /**
     * The HousepublishingAcl instance for checking permissions.
     *
     * @var HousepublishingAclInterface
     */
    protected $housepublishingAcl;

    /**
     * Constructor.
     *
     * @param HousepublishingManagerInterface $housepublishingManager The concrete HousepublishingManager service
     * @param HousepublishingAclInterface     $housepublishingAcl     The Housepublishing Acl service
     */
    public function __construct(HousepublishingManagerInterface $housepublishingManager, HousepublishingAclInterface $housepublishingAcl)
    {
        $this->realManager      = $housepublishingManager;
        $this->housepublishingAcl  = $housepublishingAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function all($limit, $offset)
    {
        $housepublishings = $this->realManager->all($limit, $offset);

        if (!$this->authorizeViewHousepublishing($housepublishings)) {
            throw new AccessDeniedException();
        }

        return $housepublishings;
    }

    /**
     * {@inheritDoc}
     */
    public function findHousepublishingBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findHousepublishingsBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAllHousepublishings(){
    }


    /**
     * {@inheritDoc}
     */
    public function saveHousepublishing(HousepublishingInterface $housepublishing)
    {
        if (!$this->housepublishingAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        $newHousepublishing = $this->isNewHousepublishing($housepublishing);

        if (!$newHousepublishing && !$this->housepublishingAcl->canEdit($housepublishing)) {
            throw new AccessDeniedException();
        }

        if (($housepublishing::STATE_DELETED === $housepublishing->getState() || $housepublishing::STATE_DELETED === $housepublishing->getPreviousState())
            && !$this->housepublishingAcl->canDelete($housepublishing)
        ) {
            throw new AccessDeniedException();
        }

        $this->realManager->saveHousepublishing($housepublishing);

        if ($newHousepublishing) {
            $this->housepublishingAcl->setDefaultAcl($housepublishing);
        }
    }

    /**
     * {@inheritDoc}
     **/
    public function findHousepublishingById($id)
    {
        $housepublishing = $this->realManager->findHousepublishingById($id);

        if (null !== $housepublishing && !$this->housepublishingAcl->canView($housepublishing)) {
            throw new AccessDeniedException();
        }

        return $housepublishing;
    }

    /**
     * {@inheritDoc}
     */
    public function createHousepublishing(QuoteInterface $quote)
    {
        return $this->realManager->createHousepublishing($quote);
    }

    /**
     * {@inheritDoc}
     */
    public function isNewHousepublishing(HousepublishingInterface $housepublishing)
    {
        return $this->realManager->isNewHousepublishing($housepublishing);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }

    /**
     * Check if the housepublishing have appropriate view permissions.
     *
     * @param  array   $housepublishings A comment tree
     * @return boolean
     */
    protected function authorizeViewHousepublishing(array $housepublishings)
    {
        foreach ($housepublishings as $housepublishing) {
            if (!$this->housepublishingAcl->canView($housepublishing)) {
                return false;
            }
        }

        return true;
    }
}
