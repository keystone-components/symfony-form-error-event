<?php

namespace Keystone\SymfonyFormErrorEvent\Type;

use Keystone\SymfonyFormErrorEvent\Event\FormErrorEvent;
use Mockery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Forms;

class ErrorEventTypeExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $eventDispatcher;
    private $factory;

    public function setUp()
    {
        $this->eventDispatcher = Mockery::mock(EventDispatcherInterface::class);

        $this->factory = Forms::createFormFactoryBuilder()
            ->addTypeExtensions([new ErrorEventTypeExtension($this->eventDispatcher)])
            ->getFormFactory();
    }

    public function testErrorEventDispatchedWhenTheFormHasErrors()
    {
        $builder = $this->factory->createBuilder(FormType::class);

        $builder->add('test', TextType::class, [
            'required' => true,
        ]);

        // Always mark the form as invalid
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                if ($form->isRoot()) {
                    $form->addError(new FormError('Test error'));
                }
            },
            1
        );

        $form = $builder->getForm();

        // Expect the form errors event to be dispatched
        $this->eventDispatcher->shouldReceive('dispatch')
            ->once()
            ->with(FormErrorEvent::ERROR, Mockery::on(function (FormErrorEvent $event) use ($form) {
                return $event->getForm() === $form;
            }));

        $form->submit([]);
    }

    public function testErrorEventDispatchedWithEventNameOption()
    {
        $builder = $this->factory->createBuilder(FormType::class, null, [
            'error_event_name' => 'test_event',
        ]);

        $builder->add('test', TextType::class, [
            'required' => true,
        ]);

        // Always mark the form as invalid
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                if ($form->isRoot()) {
                    $form->addError(new FormError('Test error'));
                }
            },
            1
        );

        $form = $builder->getForm();

        // Expect the form errors event to be dispatched
        $this->eventDispatcher->shouldReceive('dispatch')
            ->once()
            ->with('test_event', Mockery::any());

        $form->submit([]);
    }

    public function testErrorEventNotDispatchedWhenTheFormIsValid()
    {
        $builder = $this->factory->createBuilder(FormType::class);

        $builder->add('test', TextType::class, [
            'required' => false,
        ]);

        $form = $builder->getForm();

        // Expect the form errors event to be dispatched
        $this->eventDispatcher->shouldReceive('dispatch')
            ->never();

        $form->submit([]);
    }

    public function testErrorEventNotDispatchedWhenTheEventIsDisabled()
    {
        $builder = $this->factory->createBuilder(FormType::class, null, [
            'error_event' => false,
        ]);

        $builder->add('test', TextType::class, [
            'required' => true,
        ]);

        // Always mark the form as invalid
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                if ($form->isRoot()) {
                    $form->addError(new FormError('Test error'));
                }
            },
            1
        );

        $form = $builder->getForm();

        // Expect the form errors event to be dispatched
        $this->eventDispatcher->shouldReceive('dispatch')
            ->never();

        $form->submit([]);
    }
}
