<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\HousepublishingInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 */
interface HousepublishingAclInterface
{
    /**
     * Checks if the user should be able to create a housepublishing.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be able to view a housepublishing.
     *
     * @param  HousepublishingInterface $housepublishing
     * @return boolean
     */
    public function canView(HousepublishingInterface $housepublishing);

    /**
     * Checks if the user should be able to edit a housepublishing.
     *
     * @param  HousepublishingInterface $housepublishing
     * @return boolean
     */
    public function canEdit(HousepublishingInterface $housepublishing);

    /**
     * Checks if the user should be able to delete a housepublishing.
     *
     * @param  HousepublishingInterface $housepublishing
     * @return boolean
     */
    public function canDelete(HousepublishingInterface $housepublishing);

    /**
     * Sets the default Acl permissions on a housepublishing.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new HousepublishingInterface instances.
     *
     * @param  HousepublishingInterface $housepublishing
     * @return void
     */
    public function setDefaultAcl(HousepublishingInterface $housepublishing);

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
