<?php

namespace App\Form;

use App\Entity\People;
use App\Entity\Rsvp;
use App\Service\RsvpService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class PeopleType extends AbstractType
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
            ->add('mealPreference', ChoiceType::class, [
                'choices'  => [
                    "No, I'll eat whatever is in front of me" => 'meat',
                    'I do not feed myself with the living' => 'vegan',
                ],
                'label' => 'Do you have any food preference ?'
            ])
            ->add('activity', ChoiceType::class, [
                'choices'  => [
                    'Zip line sounds fun' => 'zip',
                    'keep floating' => 'float',
                ],
                'label' => 'Activity'
            ])
            ->add('yoga', ChoiceType::class, [
                'choices'  => [
                    'OMG yeeees!' => true,
                    'Do you even know me?' => false,
                ],
                'label' => 'Would you join us for yoga?'
            ])
            ->add('location', ChoiceType::class, [
                'choices'  => [
                    'Clarion' => 'hotel',
                    'Cabin' => 'cabin',
                    'Nonayobiznes' => 'other',
                ],
                'label' => 'Where will you be staying?'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer'])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => People::class,
        ]);
    }
}
