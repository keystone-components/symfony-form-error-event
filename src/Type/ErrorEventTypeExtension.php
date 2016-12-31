<?php

namespace Keystone\SymfonyFormErrorEvent\Type;

use Keystone\SymfonyFormErrorEvent\Event\FormErrorEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ErrorEventTypeExtension extends AbstractTypeExtension
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['error_event']) {
            $builder->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($options) {
                    $form = $event->getForm();
                    if ($form->isRoot() && !$form->isValid()) {
                        // Dispatch events for invalid root forms
                        $this->eventDispatcher->dispatch($options['error_event_name'], new FormErrorEvent($form));
                    }
                },
                // Set as low priority so it's called after validation
                -1
            );
        }
    }

    /**
     * @parma OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'error_event' => true,
            'error_event_name' => FormErrorEvent::ERROR,
        ]);

        $resolver->setAllowedTypes('error_event', ['bool']);
        $resolver->setAllowedTypes('error_event_name', ['string']);
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}
