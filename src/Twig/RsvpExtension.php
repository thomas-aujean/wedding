<?php

namespace App\Twig;

use App\Entity\People;
use App\Service\RsvpService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class RsvpExtension extends AbstractExtension
{
    public function __construct(protected RsvpService $rsvpService)
    {

    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('meal', [$this, 'mealPreference']),
            new TwigFilter('yoga', [$this, 'attendYoga']),
            new TwigFilter('activity', [$this, 'activity']),
            new TwigFilter('location', [$this, 'location']),
        ];
    }

    public function mealPreference(People $people): string
    {
        return $this->rsvpService->displayMealPreference($people->getMealPreference());
    }

    public function attendYoga(People $people): string
    {
        if ($people->isYoga()) {
            return 'Vous allez faire du yoga';
        }

        return 'Pas de yoga';
    }

    public function activity(People $people): string
    {
        if ($people->getActivity() === RsvpService::ACTIVITY_TUBING) {
            return 'On va descendre la rivioère !!';
        }

        return 'Zip line !!!';
    }

    public function location(People $people): string
    {
        return match ($people->getLocation()) {
            RsvpService::LOCATION_HOTEL => 'Hotel for you',
            RsvpService::LOCATION_CABIN => 'Cabins !!',
            RsvpService::LOCATION_CAMP => 'Camp ground',
            RsvpService::LOCATION_OTHER => 'Alright you do you',
        };
    }
}