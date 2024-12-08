<?php

namespace App\Controller;

use App\Entity\Rsvp;
use App\Entity\People;
use App\Form\RsvpType;
use App\Form\PeopleType;
use App\Form\PeoplePreferenceType;
use App\Service\RsvpService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class PagesController extends AbstractController
{
    #[Route('/accomodations', name: 'accomodations')]
    public function accomodations(Request $request): Response
    {
        return $this->render('pages/accomodations.html.twig', [
            'title' => 'accomodations',
        ]);
    }

    #[Route('/transportation', name: 'transportation')]
    public function transportation(Request $request): Response
    {
        return $this->renderPage(__FUNCTION__);
    }

    #[Route('/itinerary', name: 'itinerary')]
    public function itinerary(Request $request, TranslatorInterface $translator): Response
    {
        return $this->render('pages/itinerary.html.twig', [
            'title' => 'itinerary',
            'choose' => 'choose',
        ]);
    }

    #[Route('/registry', name: 'registry')]
    public function registry(Request $request): Response
    {
        return $this->renderPage(__FUNCTION__);
    }

    #[Route('/rsvp', name: 'rsvp')]
    public function rsvp(Request $request, EntityManagerInterface $entityManager, RsvpService $rsvpService): Response
    {
        $rsvp = $rsvpService->retrieve($request);

        if ($rsvp->isAttending()) {
            if ($rsvpService->isComplete($rsvp)) {

                return $this->redirectToRoute('rsvp_confirm', ['uuid' => $rsvp->getUuid()]);
            }

            return $this->redirectToRoute('rsvp_attend', ['uuid' => $rsvp->getUuid()]);
        }
        
        $people = new People();
        $form = $this->createForm(RsvpType::class, $people);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $rsvpService->handleRsvp($people, $request->getClientIp(), $entityManager);
            $request->getSession()->set('rsvp', $people->getRsvp());

            if ($people->getRsvp()->isAttending()) {
                return $this->redirectToRoute('rsvp_attend', ['uuid' => $people->getRsvp()->getUuid()]);
            }

            return $this->redirectToRoute('rsvp_no', ['uuid' => $people->getRsvp()->getUuid()]);
        }

        return $this->render('pages/rsvp.html.twig', [
            'title' => 'rsvp',
            'form' => $form,
        ]);
    }

    #[Route('/rsvp/decline/{uuid}', name: 'rsvp_no')]
    public function rsvpDecline(Rsvp $rsvp): Response
    {
        if ($rsvp->isAttending()) {
            return $this->redirectToRoute('rsvp_attend', ['uuid' => $rsvp->getUuid()]);
        }

        return $this->render('pages/rsvp_decline.html.twig', [
            'title' => 'rsvp',
            'rsvp' => $rsvp,
        ]);
    }

    #[Route('/rsvp/attend/{uuid}', name: 'rsvp_attend')]
    public function rsvpSuccess(Rsvp $rsvp, Request $request, EntityManagerInterface $entityManager, RsvpService $rsvpService): Response
    {
        $people = $rsvp->getPeople()[0];

        $form = $this->createForm(PeoplePreferenceType::class, $people);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rsvpService->handlePreference($people, $entityManager);

            return $this->redirectToRoute('rsvp_confirm', ['uuid' => $rsvp->getUuid()]);
        }

        return $this->render('pages/rsvp_attend.html.twig', [
            'title' => sprintf("Can't Wait To See You  %s!", $people->getFirstName()),
            'rsvp' => $rsvp,
            'form' => $form,
        ]);
    }

    #[Route('/rsvp/confirm/{uuid}', name: 'rsvp_confirm')]
    public function rsvpConfirm(Rsvp $rsvp, Request $request, EntityManagerInterface $entityManager, RsvpService $rsvpService, MailerInterface $mailer): Response
    {
        $people = new people();
        // $email = (new Email())
        //     ->from('hello@example.com')
        //     ->to('thomas.aujean@gmail.com')
        //     //->cc('cc@example.com')
        //     //->bcc('bcc@example.com')
        //     //->replyTo('fabien@example.com')
        //     //->priority(Email::PRIORITY_HIGH)
        //     ->subject('Time for Symfony Mailer!')
        //     ->text('Sending emails is fun again!')
        //     ->html('<p>See Twig integration for better HTML integration!</p>');

        // $mailer->send($email);
        $form = $this->createForm(PeopleType::class, $people);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $people->setRsvp($rsvp);
            $rsvpService->handlePreference($people, $entityManager);

            return $this->redirectToRoute('rsvp_confirm', ['uuid' => $rsvp->getUuid()]);
        }

        return $this->render('pages/rsvp_confirm.html.twig', [
            'title' => sprintf("Can't Wait To See You  %s!", $rsvp->getFirst()->getFirstName()),
            'rsvp' => $rsvp,
            'form' => $form,
            'first' => $rsvp->getFirst(),
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

    #[Route('/language/{lang}', name: 'language')]
    public function language(string $lang, Request $request, LocaleSwitcher $localeSwitcher): Response
    {
        $request->getSession()->set('_locale', $lang);
        $localeSwitcher->setLocale($request->getSession()->get('_locale'));

        return $this->redirect($request->headers->get('referer'));
    }

    
    #[Route('/reset/{uuid}', name: 'reset')]
    public function reset(Rsvp $rsvp, Request $request, EntityManagerInterface $entityManager): Response
    {
        foreach ($rsvp->getPeople() as $person) {
            $entityManager->remove($person);
            $entityManager->flush();
        }
        $entityManager->remove($rsvp);
        $entityManager->flush();
        $request->getSession()->remove('rsvp');

        return $this->redirectToRoute('rsvp');
    }
}
