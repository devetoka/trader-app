<?php

namespace App\DTO\User;

use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[Post(shortName: 'User')]
final class RegisterUserDTO
{
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2, max: 15,
        minMessage: 'Username should have at least 2 characters', maxMessage: 'Username should be less than 15'
    )]
    public string $username;

    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    public array $roles = [];

    #[Groups(['user:write'])]
    #[SerializedName('password')]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/')]
    public string $plainPassword;

}