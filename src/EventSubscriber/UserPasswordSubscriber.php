<?php
namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;

class UserPasswordSubscriber implements EventSubscriberInterface
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public static function getSubscribedEvents(): array
    {
        // Kernel view event, juste avant la persistence (WRITE)
        return [
            KernelEvents::VIEW => ['hashPassword', EventPriorities::PRE_WRITE],
        ];
    }

    public function hashPassword(ViewEvent $event): void
    {
        $request = $event->getRequest();
        $method = $request->getMethod();

        // Ne traiter que les POST/PUT/PATCH
        if (!in_array($method, [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH])) {
            return;
        }

        $user = $event->getControllerResult();
        if (!$user instanceof User) {
            return;
        }

        $plain = $user->getPassword();
        if (!$plain) {
            return;
        }

        // Hash et set
        $hashed = $this->passwordHasher->hashPassword($user, $plain);
        $user->setPassword($hashed);

    }
}
