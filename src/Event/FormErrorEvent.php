<?php

namespace Keystone\SymfonyFormErrorEvent\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;

class FormErrorEvent extends Event
{
    const ERROR = 'form.error';

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @param FormInterface $form
     */
    public function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param bool $deep
     * @param bool $flatten
     *
     * @return array
     */
    public function getErrors($deep = false, $flatten = true)
    {
        return $this->form->getErrors($deep, $flatten);
    }
}
