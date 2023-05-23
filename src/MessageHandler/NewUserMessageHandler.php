<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\NewUserMessage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NewUserMessageHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function __invoke(NewUserMessage $message)
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->find($message->userId);

        if ($user === null) {
            throw new EntityNotFoundException(sprintf('User with %s not found', $message->userId));
        }

        dd($user);

        // send email
    }

}