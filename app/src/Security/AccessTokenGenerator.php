<?php

namespace App\Security;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\ApiTokenRepository;
use App\Scope\Scope;
use DateTimeImmutable;

Class AccessTokenGenerator
{
    public function __construct(private ApiTokenRepository $apiTokenRepository)
    {
    }

    public function generateToken(User $user): ApiToken
    {
        $apiToken = new ApiToken();
        $apiToken->setExpiresAt(new DateTimeImmutable(ApiToken::EXPIRATION_TIME))
            ->setOwnedBy($user)
            ->setScopes(Scope::generate($user));

        $this->apiTokenRepository->save($apiToken, true);

        return $apiToken;
    }

}