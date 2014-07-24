<?php

namespace N1c0\QuoteBundle\FormFactory;

use Symfony\Component\Form\FormInterface;

/**
 * Authorsrc form creator
 */
interface AuthorsrcFormFactoryInterface
{
    /**
     * Creates a authorsrc form
     *
     * @return FormInterface
     */
    public function createForm();
}
