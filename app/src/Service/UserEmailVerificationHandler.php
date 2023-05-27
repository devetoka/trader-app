<?php
namespace App\Service;

use App\Entity\User;
use App\Exception\EmailVerificationHandlerException;
use App\Exception\InvalidTokenException;
use App\Exception\MissingUserTokenException;
use App\Exception\TokenExpiredException;
use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use Exception;

class UserEmailVerificationHandler
{
    const APP_STRING = 'trader_email_verification';
    const EXPIRATION_TIME = "+24 hours";
    const TIME_FORMAT = "Y-m-d H:i:s";

    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * @throws MissingUserTokenException
     * @throws InvalidTokenException
     * @throws Exception
     * @throws TokenExpiredException
     */
    public function handle(User $user): User
    {
        $token = $this->decode($user);
        $decoded = explode('_', $token);

        if (count($decoded) !== 4 || !filter_var($decoded[0], FILTER_VALIDATE_EMAIL)) {
            throw new EmailVerificationHandlerException('Invalid token');
        }

        $user = $this->userRepository->findOneBy(['email' => $decoded[0]]);

        if (!$user) {
            throw new EmailVerificationHandlerException('Token does not exist');
        }

        if ($user->getVerifiedAt()) {
            throw new EmailVerificationHandlerException('Email is already verified', 400);
        }

        $expirationTime =  new DateTime($decoded[2]);
        $now = new DateTime();

        if ($now > $expirationTime) {
            throw new TokenExpiredException();
        }

        $user->setVerifiedAt(new DateTimeImmutable());

        return $user;
    }

    public function encode(User $user) : string
    {
        $expirationDate = (new DateTime())->modify(self::EXPIRATION_TIME)->format(self::TIME_FORMAT);

        return bin2hex(
            base64_encode(
                sprintf(
                    "%s_%s_%s_%s",
                    $user->getEmail(),
                    $user->getUsername(),
                    $expirationDate,
                    self::APP_STRING
                )
            )
        );
    }

    /**
     * @throws MissingUserTokenException
     */
    private function decode(User $user): string
    {
        $token = $user?->getVerifiedToken();

        if (!$token) {
            throw new MissingUserTokenException();
        }

        return base64_decode(hex2bin($token));
    }

}