<?php

namespace App\Controller;

use App\Entity\Rsvp;
use App\Entity\People;
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
    #[Route('/people/edit/{uuid}', name: 'people_edit')]
    public function peopleEdit(People $people, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($people)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('mealPreference', ChoiceType::class, [
                'choices'  => [
                    'I want meat' => 'meat',
                    'flowers for me' => 'vegan',
                ],
                'label' => 'What you eat'
            ])
            ->add('activity', ChoiceType::class, [
                'choices'  => [
                    'Zip line sounds fun' => 'zip',
                    'keep floating' => 'float',
                ],
                'label' => 'Activity'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer'])
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
