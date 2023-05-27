<?php

namespace App\State\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Serializer\AbstractCollectionNormalizer;
use ApiPlatform\Serializer\AbstractItemNormalizer;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Message\ForgotPasswordMessage;
use http\Exception\InvalidArgumentException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ForgotPasswordProcessor implements ProcessorInterface
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!isset($context['resource_class'], $data->email) || $context['resource_class'] !== User::class) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->messageBus->dispatch(new ForgotPasswordMessage($data->email));

        return $data;

    }
}