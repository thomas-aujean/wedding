<?php

namespace App\Form;

use App\Entity\People;
use App\Service\RsvpService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
                'choices'  => $this->service->formMealPreferences(),
                'label' => $this->translator->trans('dinner_selection')
            ])
            ->add('activity', ChoiceType::class, [
                'choices'  => $this->service->formActivities(),
                'label' => $this->translator->trans('activity')
            ])
            ->add('yoga', ChoiceType::class, [
                'choices'  => [
                    'Namasté' => true,
                    'Nah, I’mma stay in bed' => false,
                ],
                'label' => $this->translator->trans('outdoor_yoga')
            ])
            ->add('location', ChoiceType::class, [
                'choices'  => $this->service->formLocations(),
                'label' => $this->translator->trans('where_will_you_stay')
            ])
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
