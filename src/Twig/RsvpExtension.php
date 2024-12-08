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
            return 'Yes';
        }

        return 'No';
    }

    public function activity(People $people): string
    {
        return match ($people->getActivity()) {
            RsvpService::ACTIVITY_ZIP => 'Zipline',
            RsvpService::ACTIVITY_TUBING => 'Whitewater tubing',
            RsvpService::ACTIVITY_POOL => 'Pool day',
            RsvpService::ACTIVITY_OTHER => 'Other',
        };
    }

    public function location(People $people): string
    {
        return match ($people->getLocation()) {
            RsvpService::LOCATION_HOTEL => 'Clarion Hotel',
            RsvpService::LOCATION_CABIN_S => 'Small cabin',
            RsvpService::LOCATION_CABIN_L => 'Large cabin',
            RsvpService::LOCATION_CAMP => 'Campsite',
            RsvpService::LOCATION_OTHER => 'OOther',
        };
    }
}