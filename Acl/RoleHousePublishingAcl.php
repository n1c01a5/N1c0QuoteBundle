<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\HousePublishingBundle\Model\HousePublishingInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements Role checking using the Symfony2 Security component
 */
class RoleHousePublishingAcl implements HousePublishingAclInterface
{
    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The FQCN of the HousePublishing object.
     *
     * @var string
     */
    private $housePublishingClass;

    /**
     * The role that will grant create permission for a housePublishing.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a housePublishing.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a housePublishing.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a housePublishing.
     *
     * @var string
     */
    private $deleteRole;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface $securityContext
     * @param string                   $createRole
     * @param string                   $viewRole
     * @param string                   $editRole
     * @param string                   $deleteRole
     * @param string                   $housePublishingClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $housePublishingClass
    )
    {
        $this->securityContext   = $securityContext;
        $this->createRole        = $createRole;
        $this->viewRole          = $viewRole;
        $this->editRole          = $editRole;
        $this->deleteRole        = $deleteRole;
        $this->housePublishingClass      = $housePublishingClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new HousePublishing.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified HousePublishing.
     *
     * @param  HousePublishingInterface $housePublishing
     * @return boolean
     */
    public function canView(HousePublishingInterface $housePublishing)
    {
        return $this->securityContext->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token is allowed to reply to a parent housePublishing.
     *
     * @param  HousePublishingInterface|null $parent
     * @return boolean
     */
    public function canReply(HousePublishingInterface $parent = null)
    {
        if (null !== $parent) {
            return $this->canCreate() && $this->canView($parent);
        }

        return $this->canCreate();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied HousePublishing.
     *
     * @param  HousePublishingInterface $housePublishing
     * @return boolean
     */
    public function canEdit(HousePublishingInterface $housePublishing)
    {
        return $this->securityContext->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific HousePublishing.
     *
     * @param  HousePublishingInterface $housePublishing
     * @return boolean
     */
    public function canDelete(HousePublishingInterface $housePublishing)
    {
        return $this->securityContext->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param  HousePublishingInterface $housePublishing
     * @return void
     */
    public function setDefaultAcl(HousePublishingInterface $housePublishing)
    {

    }

    /**
     * Role based Acl does not require setup.
     *
     * @return void
     */
    public function installFallbackAcl()
    {

    }

    /**
     * Role based Acl does not require setup.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {

    }
}
