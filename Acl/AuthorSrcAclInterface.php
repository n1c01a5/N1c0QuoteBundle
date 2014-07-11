<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\AuthorSrcInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 */
interface AuthorSrcAclInterface
{
    /**
     * Checks if the user should be able to create a authorSrc.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be able to view a authorSrc.
     *
     * @param  AuthorSrcInterface $authorSrc
     * @return boolean
     */
    public function canView(AuthorSrcInterface $authorSrc);

    /**
     * Checks if the user should be able to edit a authorSrc.
     *
     * @param  AuthorSrcInterface $authorSrc
     * @return boolean
     */
    public function canEdit(AuthorSrcInterface $authorSrc);

    /**
     * Checks if the user should be able to delete a authorSrc.
     *
     * @param  AuthorSrcInterface $authorSrc
     * @return boolean
     */
    public function canDelete(AuthorSrcInterface $authorSrc);

    /**
     * Sets the default Acl permissions on a authorSrc.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new AuthorSrcInterface instances.
     *
     * @param  AuthorSrcInterface $authorSrc
     * @return void
     */
    public function setDefaultAcl(AuthorSrcInterface $authorSrc);

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
