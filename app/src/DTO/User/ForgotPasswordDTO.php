<?php

namespace App\DTO\User;

use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[Post(shortName: 'User')]
final class ForgotPasswordDTO
{
    #[Groups(['email:forgot'])]
    #[Assert\NotBlank(groups: ['email:forgot'])]
    #[Assert\Email(groups: ['email:forgot'])]
    public string $email;

}