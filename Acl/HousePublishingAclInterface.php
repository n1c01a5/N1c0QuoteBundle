<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\HousePublishingInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 */
interface HousePublishingAclInterface
{
    /**
     * Checks if the user should be able to create a housePublishing.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be able to view a housePublishing.
     *
     * @param  HousePublishingInterface $housePublishing
     * @return boolean
     */
    public function canView(HousePublishingInterface $housePublishing);

    /**
     * Checks if the user should be able to edit a housePublishing.
     *
     * @param  HousePublishingInterface $housePublishing
     * @return boolean
     */
    public function canEdit(HousePublishingInterface $housePublishing);

    /**
     * Checks if the user should be able to delete a housePublishing.
     *
     * @param  HousePublishingInterface $housePublishing
     * @return boolean
     */
    public function canDelete(HousePublishingInterface $housePublishing);

    /**
     * Sets the default Acl permissions on a housePublishing.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new HousePublishingInterface instances.
     *
     * @param  HousePublishingInterface $housePublishing
     * @return void
     */
    public function setDefaultAcl(HousePublishingInterface $housePublishing);

    /**
     * Installs the Default 'fallback' Acl entries for generic access.
     *
     * @return void
     */
    public function installFallbackAcl();

    /**
     * Removes default Acl entries
     * @return void
     */
    public function uninstallFallbackAcl();
}
