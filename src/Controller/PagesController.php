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

class PagesController extends AbstractController
{
    #[Route('/accomodations', name: 'accomodations')]
    public function accomodations(Request $request): Response
    {
        return $this->renderPage(__FUNCTION__);
    }

    #[Route('/transportation', name: 'transportation')]
    public function transportation(Request $request): Response
    {
        return $this->renderPage(__FUNCTION__);
    }

    #[Route('/itinerary', name: 'itinerary')]
    public function itinerary(Request $request): Response
    {
        return $this->renderPage(__FUNCTION__);
    }

    #[Route('/registry', name: 'registry')]
    public function registry(Request $request): Response
    {
        return $this->renderPage(__FUNCTION__);
    }

    #[Route('/rsvp', name: 'rsvp')]
    public function rsvp(Request $request, EntityManagerInterface $entityManager): Response
    {
        // if ($request->getSession()->get('rsvp')) {
            // return $this->redirectToRoute('rsvp_edit', ['uuid' => $request->getSession()->get('rsvp')->getUuid()]);
        // }

        // ADD IP CHECK !!

        $rsvp = new Rsvp();

        $form = $this->createFormBuilder($rsvp)
            ->add('isAttending', ChoiceType::class, [
                'choices'  => [
                    'Of course !' => true,
                    'Unfortunately no' => false,
                ],
                'label' => 'Will you be attending our wedding'
            ])
            ->add('name', TextType::class)
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $rsvp = $form->getData();
            $rsvp->setUuid(Uuid::v4());
            $rsvp->setIpAddress($request->getClientIp());

            $entityManager->persist($rsvp);
            $entityManager->flush();

            $request->getSession()->set('rsvp', $rsvp);

            return $this->redirectToRoute('rsvp_edit', ['uuid' => $rsvp->getUuid()]);
        }

        return $this->render('pages/rsvp.html.twig', [
            'title' => 'rsvp',
            'form' => $form,
        ]);
    }


    #[Route('/rsvp/edit/{uuid}', name: 'rsvp_edit')]
    public function rsvpSuccess(Rsvp $rsvp, Request $request, EntityManagerInterface $entityManager): Response
    {
        $people = new People();

        $form = $this->createFormBuilder($people)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class, [
                'attr' => [
                    'value' => $rsvp->getName(),
                ],
            ])
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
            $people->setRsvp($rsvp);
            $people->setUuid(Uuid::v4());

            $entityManager->persist($people);
            $entityManager->flush();

            return $this->redirectToRoute('rsvp_edit', ['uuid' => $rsvp->getUuid()]);
        }

        return $this->render('pages/rsvp_edit.html.twig', [
            'title' => 'RSVP famille ' . $rsvp->getName(),
            'rsvp' => $rsvp,
            'form' => $form,
        ]);
    }

    /**
     * Renders all methods the same way
     */
    public function renderPage(string $method): Response
    {
        return $this->render('pages/' . mb_strtolower($method) . '.html.twig', [
            'title' => $method,
        ]);
    }
}
