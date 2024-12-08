<?php

namespace App\Controller;

use App\Entity\Rsvp;
use App\Entity\People;
use App\Service\RsvpService;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Uid\Uuid;

class PeopleController extends AbstractController
{
    
    private $service;
    private $translator;

    public function __construct(RsvpService $service, TranslatorInterface $translator)
    {
        $this->service = $service;
        $this->translator = $translator;
    }

    #[Route('/people/edit/{uuid}', name: 'people_edit')]
    public function peopleEdit(People $people, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($people)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('mealPreference', ChoiceType::class, [
                'choices'  => $this->service->formMealPreferences(),
                'label' => 'Dinner selection'
            ])
            ->add('activity', ChoiceType::class, [
                'choices'  => [
                    'Zip lining' => 'zip',
                    'Whitewater tubing' => 'float',
                    'Pool day' => 'pool',
                    'None/Other' => 'other',
                ],
                'label' => 'Activity'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $people = $form->getData();

            $entityManager->persist($people);
            $entityManager->flush();
        }
        //send email with url for update 
        return $this->render('people/edit.html.twig', [
            'rsvp' => $people->getRsvp(),
            'form' => $form,
            'people' => $people,
        ]);
    }
    #[Route('/people/delete/{uuid}', name: 'people_delete')]
    public function peopleDelete(People $people, Request $request, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($people);
        $entityManager->flush();


        return $this->redirectToRoute('rsvp_edit', ['uuid' => $people->getRsvp()->getUuid()]);
    }
}
