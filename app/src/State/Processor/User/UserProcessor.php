<?php

namespace App\State\Processor\User;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Exception\InvalidTokenException;
use App\Exception\MissingUserTokenException;
use App\Exception\TokenExpiredException;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsDecorator('api_platform.doctrine.orm.state.persist_processor')]
class UserProcessor implements ProcessorInterface
{
    public function __construct(
     private PostOperation $postOperation,
     private PatchOperation $patchOperation,
     private DeleteOperation $deleteOperation,
     private ProcessorInterface $innerProcessor,
    )
    {
    }

    /**
     * @throws TokenExpiredException
     * @throws InvalidTokenException
     * @throws MissingUserTokenException
     */
    public function process(mixed $data, HttpOperation|Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof User) {
            return $this;
        }

        $operator = match (strtoupper($operation->getMethod())) {
            'POST' =>  $this->postOperation->mutate($data, $context),
            'PATCH' =>  $this->patchOperation->mutate($data, $context),
            'DELETE' =>  $this->deleteOperation->mutate($data, $context),
            default => throw new HttpException(400, 'Invalid http method', )
        };

        $data = $operator->getData();

        $this->innerProcessor->process($data, $operation, $uriVariables, $context);

        $operator->postProcessorOperation($data);
    }
}