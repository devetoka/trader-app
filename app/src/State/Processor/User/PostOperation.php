<?php

namespace App\State\Processor\User;

use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\BaseEntity;
use App\Entity\User;
use App\Exception\InvalidTokenException;
use App\Exception\MissingUserTokenException;
use App\Exception\TokenExpiredException;
use App\Message\NewUserMessage;
use App\Service\UserEmailVerificationHandler;
use App\State\Processor\MutatorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Exception\LogicException;

class PostOperation implements MutatorInterface
{
    private ?BaseEntity $mutatedData = null;
    private const POST_CREATE = "create";
    private const POST_VERIFY = "verify";
    private const POST_PASSWORD = "forgot_password";
    private string $postType;

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserEmailVerificationHandler $emailVerificationHandler,
        private MessageBusInterface $messageBus
    ){
    }


    /**
     * @throws InvalidTokenException
     * @throws MissingUserTokenException
     * @throws TokenExpiredException
     */
    public function mutate(mixed $data, array $context = []) : self
    {
        if (!array_key_exists('operation', $context) || !$context['operation'] instanceof Post) {
            throw new LogicException();
        }
        if ($context['operation']->getName() == 'user.register' && $data->getPlainPassword()) {
            $this->postType = self::POST_CREATE;

            $data->setPassword($this->userPasswordHasher->hashPassword($data, $data->getPlainPassword()));
        }


        if ($context['operation']->getName() == 'user.verify' && $data->getVerifiedToken()) {
            $this->postType = self::POST_VERIFY;

            $data = $this->emailVerificationHandler->handle($data);
        }

        $this->mutatedData = $data;

        return $this;
    }

    public function postProcessorOperation(BaseEntity $data): void
    {
        /* @var User $data */
        if ($this->postType == self::POST_CREATE) {
            $this->messageBus->dispatch(new NewUserMessage($data->getId()));
        }
    }

    public function getData(): BaseEntity
    {
        return $this->mutatedData;
    }
}