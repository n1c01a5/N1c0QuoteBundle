<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\TagInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 */
interface TagAclInterface
{
    /**
     * Checks if the user should be able to create a tag.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be able to view a tag.
     *
     * @param  TagInterface $tag
     * @return boolean
     */
    public function canView(TagInterface $tag);

    /**
     * Checks if the user should be able to edit a tag.
     *
     * @param  TagInterface $tag
     * @return boolean
     */
    public function canEdit(TagInterface $tag);

    /**
     * Checks if the user should be able to delete a tag.
     *
     * @param  TagInterface $tag
     * @return boolean
     */
    public function canDelete(TagInterface $tag);

    /**
     * Sets the default Acl permissions on a tag.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new TagInterface instances.
     *
     * @param  TagInterface $tag
     * @return void
     */
    public function setDefaultAcl(TagInterface $tag);

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
