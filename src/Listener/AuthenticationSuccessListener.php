<?php

namespace App\Listener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

class AuthenticationSuccessListener
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onAuthenticationSuccess(ResponseEvent $event)
    {
        $request = $event->getRequest();

        // Vérifier si le chemin de la requête correspond à /api/auth
        if ($request->getPathInfo() !== '/auth') {
            return;
        }

        // Récupérer l'utilisateur authentifié
        $user = $this->security->getUser();
        
        // Si l'utilisateur est authentifié et est une instance de User
        if ($user instanceof User) {
            // Décoder le contenu de la réponse (token JWT)
            $data = json_decode($event->getResponse()->getContent(), true);

            // Ajouter les informations de l'utilisateur à la réponse
            $data['user'] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'nom' => $user->getNom(),
                // 'prenom' => $user->getPrenom(),
                'roles' => $user->getRoles(),
                // 'picto' => $user->getPicto()
            ];

            // Modifier le contenu de la réponse avec les informations supplémentaires
            $event->getResponse()->setContent(json_encode($data));
        }
    }
}
