<?php

namespace N1c0\QuoteBundle\FormFactory;

use Symfony\Component\Form\FormInterface;

/**
 * Book form creator
 */
interface BookFormFactoryInterface
{
    /**
     * Creates a book form
     *
     * @return FormInterface
     */
    public function createForm();
}
