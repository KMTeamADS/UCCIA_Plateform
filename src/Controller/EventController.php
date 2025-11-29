<?php

declare(strict_types=1);

namespace ADS\UCCIA\Controller;

use ADS\UCCIA\Entity\Event;
use ADS\UCCIA\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/events', name: 'app_event_')]
final class EventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {
    }

    #[Route('/{slug}', name: 'show', requirements: ['slug' => '[a-z0-9\-]*'], methods: ['GET'])]
    public function show(string $slug, Request $request): Response
    {
        $event = $this->eventRepository->findEvent($slug, $request->getLocale());

        if (!$event instanceof Event) {
            throw $this->createNotFoundException('Event not found');
        }

        return $this->render('app/event/show.html.twig', [
            'controller_name' => 'EventController',
            'event' => $event,
        ]);
    }
}
