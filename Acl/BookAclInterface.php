<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\BookInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 */
interface BookAclInterface
{
    /**
     * Checks if the user should be able to create a book.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be able to view a book.
     *
     * @param  BookInterface $book
     * @return boolean
     */
    public function canView(BookInterface $book);

    /**
     * Checks if the user should be able to edit a book.
     *
     * @param  BookInterface $book
     * @return boolean
     */
    public function canEdit(BookInterface $book);

    /**
     * Checks if the user should be able to delete a book.
     *
     * @param  BookInterface $book
     * @return boolean
     */
    public function canDelete(BookInterface $book);

    /**
     * Sets the default Acl permissions on a book.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new BookInterface instances.
     *
     * @param  BookInterface $book
     * @return void
     */
    public function setDefaultAcl(BookInterface $book);

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
