<?php

declare(strict_types=1);

namespace ADS\UCCIA\Routing\Strategy;

use ADS\UCCIA\Entity\Event;
use ADS\UCCIA\Repository\EventRepository;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class EventTranslateRouteParams implements RouteParamsTranslationStrategyInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private EventRepository $eventRepository,
    ) {
    }

    #[\Override] public function translate(string $routeName, array $currentParams, string $targetLocale): array
    {
        $currentSlug = $currentParams['slug'] ?? '';
        // $currentRequestLocale = $currentParams['_locale'];
        $currentRequestLocale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr';

        $event = $this->eventRepository->findEvent($currentSlug, $currentRequestLocale);

        if (!$event instanceof Event) {
            return $currentParams;
        }

        return ['slug' => $event->translate($targetLocale)->getSlug()];
    }

    #[\Override] public static function supports(): string
    {
        return 'app_event_show';
    }
}
