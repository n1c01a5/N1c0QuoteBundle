<?php

namespace N1c0\QuoteBundle\FormFactory;

use Symfony\Component\Form\FormInterface;

/**
 * Quote form creator
 */
interface QuoteFormFactoryInterface
{
    /**
     * Creates a quote form
     *
     * @return FormInterface
     */
    public function createForm();
}
