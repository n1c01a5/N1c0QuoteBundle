<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\HousePublishingInterface;
use N1c0\DissertationBundle\Model\HousePublishingManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wraps a real implementation of HousePublishingManagerInterface and
 * performs Acl checks with the configured HousePublishing Acl service.
 */
class AclHousePublishingManager implements HousePublishingManagerInterface
{
    /**
     * The HousePublishingManager instance to be wrapped with ACL.
     *
     * @var HousePublishingManagerInterface
     */
    protected $realManager;

    /**
     * The HousePublishingAcl instance for checking permissions.
     *
     * @var HousePublishingAclInterface
     */
    protected $housePublishingAcl;

    /**
     * Constructor.
     *
     * @param HousePublishingManagerInterface $housePublishingManager The concrete HousePublishingManager service
     * @param HousePublishingAclInterface     $housePublishingAcl     The HousePublishing Acl service
     */
    public function __construct(HousePublishingManagerInterface $housePublishingManager, HousePublishingAclInterface $housePublishingAcl)
    {
        $this->realManager      = $housePublishingManager;
        $this->housePublishingAcl  = $housePublishingAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function all($limit = 5, $offset = 0)
    {
        $housePublishings = $this->realManager->all();

        if (!$this->authorizeViewHousePublishing($housePublishings)) {
            throw new AccessDeniedException();
        }

        return $housePublishings;
    }

    /**
     * {@inheritDoc}
     */
    public function findHousePublishingBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findHousePublishingsBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAllHousePublishings(){
    }                 


    /**
     * {@inheritDoc}
     */
    public function saveHousePublishing(HousePublishingInterface $housePublishing)
    {
        if (!$this->housePublishingAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        $newHousePublishing = $this->isNewHousePublishing($housePublishing);

        if (!$newHousePublishing && !$this->housePublishingAcl->canEdit($housePublishing)) {
            throw new AccessDeniedException();
        }

        if (($housePublishing::STATE_DELETED === $housePublishing->getState() || $housePublishing::STATE_DELETED === $housePublishing->getPreviousState())
            && !$this->housePublishingAcl->canDelete($housePublishing)
        ) {
            throw new AccessDeniedException();
        }

        $this->realManager->saveHousePublishing($housePublishing);

        if ($newHousePublishing) {
            $this->housePublishingAcl->setDefaultAcl($housePublishing);
        }
    }

    /**
     * {@inheritDoc}
     **/
    public function findHousePublishingById($id)
    {
        $housePublishing = $this->realManager->findHousePublishingById($id);

        if (null !== $housePublishing && !$this->housePublishingAcl->canView($housePublishing)) {
            throw new AccessDeniedException();
        }

        return $housePublishing;
    }

    /**
     * {@inheritDoc}
     */
    public function createHousePublishing($id = null)
    {
        return $this->realManager->createHousePublishing($id);
    }

    /**
     * {@inheritDoc}
     */
    public function isNewHousePublishing(HousePublishingInterface $housePublishing)
    {
        return $this->realManager->isNewHousePublishing($housePublishing);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }

    /**
     * Check if the housePublishing have appropriate view permissions.
     *
     * @param  array   $housePublishings A comment tree
     * @return boolean
     */
    protected function authorizeViewHousePublishing(array $housePublishings)
    {
        foreach ($housePublishings as $housePublishing) {
            if (!$this->housePublishingAcl->canView($housePublishing)) {
                return false;
            }
        }

        return true;
    }
}
