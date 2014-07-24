<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\AuthorsrcInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 */
interface AuthorsrcAclInterface
{
    /**
     * Checks if the user should be able to create a authorsrc.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be able to view a authorsrc.
     *
     * @param  AuthorsrcInterface $authorsrc
     * @return boolean
     */
    public function canView(AuthorsrcInterface $authorsrc);

    /**
     * Checks if the user should be able to edit a authorsrc.
     *
     * @param  AuthorsrcInterface $authorsrc
     * @return boolean
     */
    public function canEdit(AuthorsrcInterface $authorsrc);

    /**
     * Checks if the user should be able to delete a authorsrc.
     *
     * @param  AuthorsrcInterface $authorsrc
     * @return boolean
     */
    public function canDelete(AuthorsrcInterface $authorsrc);

    /**
     * Sets the default Acl permissions on a authorsrc.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new AuthorsrcInterface instances.
     *
     * @param  AuthorsrcInterface $authorsrc
     * @return void
     */
    public function setDefaultAcl(AuthorsrcInterface $authorsrc);

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
