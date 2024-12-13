<?php

namespace App\Form;

use App\Entity\Rsvp;
use App\Service\RsvpService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotNull;

class AttendingType extends AbstractType
{
    private $service;
    private $translator;

    public function __construct(RsvpService $service, TranslatorInterface $translator)
    {
        $this->service = $service;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isAttending', ChoiceType::class, [
                'choices'  => $this->service->attendingOptions(),
                'label' => $this->translator->trans('see_ya'),
                'constraints' => [new NotNull()],
                'placeholder' => $this->translator->trans('select'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rsvp::class,
        ]);
    }
}
