<?php

namespace App\Service;

use App\Controller\PeopleController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Rsvp;
use App\Entity\People;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\EntityManagerInterface;

class RsvpService
{
    protected $translator;

    const MEAL_VEGAN = 'vegan';
    const MEAL_ALL = 'meat';

    const ACTIVITY_ZIP = 'zipline';
    const ACTIVITY_TUBING = 'tubing';

    const LOCATION_HOTEL = 'hotel';
    const LOCATION_CABIN = 'cabin';
    const LOCATION_CAMP = 'camp';
    const LOCATION_OTHER = 'other';

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Returns available attending responses.
     */
    public function attendingOptions(): array
    {
        return [
            $this->translator->trans('rsvp_yes') => true,
            $this->translator->trans('rsvp_no') => false,
        ];
    }

    /**
     * Returns available attending responses.
     */
    public function mealPreferences(): array
    {
        return [
            self::MEAL_VEGAN => 'Delicious vegan meal',
            self::MEAL_ALL => 'All you can eat',
        ];
    }
/**
 * 
 */
public function displayMealPreference(string $preference): string 
{
    return $this->mealPreferences()[$preference];
}

    /**
     * Handles RSVP main response from the user.
     */
    public function handleRsvp(People $people, string $userIp, EntityManagerInterface $entityManager): People
    {
        $people->getRsvp()->setUuid(Uuid::v4());
        $people->getRsvp()->setIpAddress($userIp);
        $entityManager->persist($people->getRsvp());

        $people->setUuid(Uuid::v4());

        
        $entityManager->persist($people);
        $entityManager->flush();

        return $people;
    }

    /**
     * Retrievs current Rsvp
     */
    public function retrieve(Request $request): Rsvp
    {

       if ($request->getSession()->get('rsvp')) {
        return $request->getSession()->get('rsvp');
        }

        // ADD IP CHECK !!
        // $request->getClientIp();

        return new Rsvp();
    }

    public function handlePreference(People $people, EntityManagerInterface $entityManager)
    {
        $people->setUuid(Uuid::v4());
        $entityManager->persist($people);
        $entityManager->flush();
    }
}
