<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\AccessTokenGenerator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SecurityController extends AbstractController
{
    public function __construct(private Security $security, private AccessTokenGenerator $tokenGenerator)
    {
    }

    #[Route('/api/login', name: 'app_login', methods: ['POST'])]
    public function login(#[CurrentUser] User $user = null): Response
    {
        if (!$user) {
            return $this->json([
                'error' => 'Invalid login request: check that the Content-Type header is "application/json".',
            ], 401);
        }
        $apiToken = $this->tokenGenerator->generateToken($user);

        return $this->json([
            'access_token' => $apiToken->getToken(),
            'expires_at' => $apiToken->getExpiresAt(),
            'scopes' => $apiToken->getScopes()
        ]);
    }

    #[Route('/logout', name: 'app_api_logout', methods: ['GET'])]
    public function log(): Response
    {
        return $this->json([
            'message' => 'Logout successful',
        ]);
    }

    #[Route('/api/logout-response', name: 'logout_response', methods: ['GET'])]
    public function log2(): Response
    {
        dd($this->security->getUser());
    }

}
