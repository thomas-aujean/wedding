<?php

namespace App\Form;

use App\Entity\People;
use App\Entity\Rsvp;
use App\Service\RsvpService;
use App\Form\PeopleNameType;
use App\Form\AttendingType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;

class RsvpType extends AbstractType
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
            ->add('firstName', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank()],
                'label' => $this->translator->trans('first_name')
            ])

            ->add('lastName', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank()],
                'label' => $this->translator->trans('last_name')
            ])
            ->add('rsvp', AttendingType::class)
            ->add('submit', SubmitType::class, ['label' => $this->translator->trans('submit')])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => People::class,
        ]);
    }
}
