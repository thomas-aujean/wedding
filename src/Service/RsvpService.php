<?php

namespace App\Service;

use App\Controller\PeopleController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Rsvp;
use App\Entity\People;
use App\Repository\PeopleRepository;
use App\Repository\RsvpRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\EntityManagerInterface;

class RsvpService
{
    protected $translator;

    const MEAL_VEGAN = 'vegan';
    const MEAL_ALL = 'meat';
    const MEAL_FISH = 'fish';

    const ACTIVITY_ZIP = 'zip';
    const ACTIVITY_TUBING = 'float';
    const ACTIVITY_POOL = 'pool';
    const ACTIVITY_OTHER = 'other';

    const LOCATION_HOTEL = 'hotel';
    const LOCATION_CABIN_S = 'small_cabin';
    const LOCATION_CABIN_L = 'large_cabin';
    const LOCATION_CAMP = 'campsite';
    const LOCATION_OTHER = 'other';

    public function __construct(
        TranslatorInterface $translator,
        protected RsvpRepository $rsvpRepository,
        protected PeopleRepository $peopleRepository
    ) {
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
            self::MEAL_VEGAN => 'Vegan',
            self::MEAL_ALL => 'Meat',
            self::MEAL_FISH => 'Fish',
        ];
    }

    /**
     * Returns available attending responses.
     */
    public function formMealPreferences(): array
    {
        return [
            'Vegan' => self::MEAL_VEGAN,
            'Meat' => self::MEAL_ALL,
            'Fish' => self::MEAL_FISH,
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
        $people->getRsvp()->addPerson($people);
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

        $sessionRsvp = $request->getSession()->get('rsvp');
        if (!is_null($sessionRsvp) && !is_null($this->rsvpRepository->find($sessionRsvp->getId()))) {
            $rsvp = $this->rsvpRepository->find($sessionRsvp->getId());
            $request->getSession()->set('rsvp', $rsvp);

            return $rsvp;
        }

        // ADD IP CHECK !!
        // $request->getClientIp();

        return new Rsvp();
    }

    /**
     * 
     */
    public function handlePreference(People $people, EntityManagerInterface $entityManager)
    {
        $people->setUuid(Uuid::v4());
        $entityManager->persist($people);
        $entityManager->flush();
    }

    /**
     * 
     */
    public function isComplete(Rsvp $rsvp): bool
    {
        $mainPerson = $rsvp->getFirst();
        if ($mainPerson === false) {
            throw new \Exception('Houston, we have a problem !');
        }
        $mainPerson = $this->peopleRepository->find($mainPerson->getId());

        return !is_null($mainPerson->getActivity()) || !is_null($mainPerson->getLocation()) || !is_null($mainPerson->getMealPreference()) || !is_null($mainPerson->isYoga());
    }
}
