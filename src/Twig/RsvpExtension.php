<?php

namespace App\Twig;

use App\Entity\People;
use App\Service\RsvpService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Symfony\Contracts\Translation\TranslatorInterface;

class RsvpExtension extends AbstractExtension
{
    public function __construct(
        protected TranslatorInterface $translator,
        protected RsvpService $rsvpService)
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
            RsvpService::ACTIVITY_ZIP => $this->translator->trans('Zip'),
            RsvpService::ACTIVITY_TUBING => $this->translator->trans('Tubing'),
            RsvpService::ACTIVITY_POOL => $this->translator->trans('Pool'),
            RsvpService::ACTIVITY_OTHER => $this->translator->trans('Other'),
            default => 'Unknown'
        };
    }

    public function location(People $people): string
    {
        return match ($people->getLocation()) {
            RsvpService::LOCATION_HOTEL => 'Clarion Hotel',
            RsvpService::LOCATION_CABIN_S => $this->translator->trans('cabin_s'),
            RsvpService::LOCATION_CABIN_L => $this->translator->trans('cabin_l'),
            RsvpService::LOCATION_CAMP => $this->translator->trans('Campsite'),
            RsvpService::LOCATION_OTHER => $this->translator->trans('Other'),
            default => 'Unknown'
        };
    }
}