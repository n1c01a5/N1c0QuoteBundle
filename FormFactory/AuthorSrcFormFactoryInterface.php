<?php

namespace N1c0\DissertationBundle\FormFactory;

use Symfony\Component\Form\FormInterface;

/**
 * AuthorSrc form creator
 */
interface AuthorSrcFormFactoryInterface
{
    /**
     * Creates a authorSrc form
     *
     * @return FormInterface
     */
    public function createForm();
}
