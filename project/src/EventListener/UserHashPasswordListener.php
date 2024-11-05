<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserHashPasswordListener implements EventSubscriberInterface
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['hashPassword',EventPriorities::PRE_WRITE],
        ];
    }
    public function hashPassword(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $methode = $event ->getRequest()->getMethod();

        if ($entity instanceof User && ($methode === Request::METHOD_POST || $methode === Request::METHOD_PUT)) {
            $hashedPassword = $this->passwordHasher->hashPassword($entity, $entity->getPassword());
            $entity -> setPassword($hashedPassword);
        }
    }
}