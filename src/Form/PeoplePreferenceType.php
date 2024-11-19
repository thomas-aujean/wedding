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

class PeoplePreferenceType extends AbstractType
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
            ->add('mealPreference', ChoiceType::class, [
                'choices'  => [
                    "Plutôt viande " => 'meat',
                    'Plutôt poisson' => 'fish',
                    'Plutôt crever' => 'vegan',
                ],
                'label' => 'Dinner selection'
            ])
            ->add('activity', ChoiceType::class, [
                'choices'  => [
                    'Zip lining' => 'zip',
                    'River tubing' => 'float',
                ],
                'label' => 'Activity'
            ])
            ->add('yoga', ChoiceType::class, [
                'choices'  => [
                    'Namasté' => true,
                    'Nah, I’mma stay in bed' => false,
                ],
                'label' => 'Outdoor Yoga'
            ])
            ->add('location', ChoiceType::class, [
                'choices'  => [
                    'Clarion Hotel' => 'hotel',
                    'Small cabin' => 'small_cabin',
                    'Large Cabin' => 'large_cabin',
                    'Other' => 'other',
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
