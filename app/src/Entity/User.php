<?php

namespace App\Entity;

use ApiPlatform\Action\PlaceholderAction;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\EmailVerificationController;
use App\DTO\User\ForgotPasswordDTO;
use App\Operations\UserEndpoints;
use App\Repository\UserRepository;
use App\State\Processor\User\ForgotPasswordProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
            new Get(),
            new Post(
                name: 'user.register',
                uriTemplate: '/register',
                security: 'is_granted("PUBLIC_ACCESS")'
            ),
            new Patch(),
            new Delete(),
            new GetCollection()
    ],
    normalizationContext: [
        'skip_null_values' => false,
        'groups' => ['user:read']
    ],
    denormalizationContext: ['groups' => ['user:write']],
    security: "is_granted('ROLE_USER')",
)]

#[ApiResource(
    operations: [
        new Post(
            name: 'user.verify',
            uriTemplate: '/verify',
            openapiContext: [
                "summary" => "verifies a user resource"
            ],
            denormalizationContext: ['groups' => ['email:verify']],
            normalizationContext: ['groups' => ['email:verify:read']],
            validationContext: ['groups' => ['email:verify']],
        ),
        new Post(
            uriTemplate: '/forgot-password',
            status: 204,
            openapiContext: [
                "summary" => "Sends a mail for password reset",
                "response" => []
            ],
            normalizationContext: ['groups' => ['email:forgot:read']],
            denormalizationContext: ['groups' => ['email:forgot']],
            validationContext: ['groups' => ['email:forgot']],
            input: ForgotPasswordDTO::class,
            name: 'user.forgot-password',
            processor: ForgotPasswordProcessor::class,
        ),
        new Post(
            uriTemplate: '/reset-password',
            openapiContext: [
                "summary" => "Sends a mail for password reset"
            ],
            denormalizationContext: ['groups' => ['email:reset']]
        )
    ],

    security: "is_granted('PUBLIC_ACCESS')"

)]

#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['username'], message: 'It looks like another user took your username. Sorry!')]
class User extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank()]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2, max: 15,
        minMessage: 'Username should have at least 2 characters', maxMessage: 'Username should be less than 15'
    )]
    private ?string $username = null;


    #[ORM\Column]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['user:write'])]
    #[SerializedName('password')]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/')]
    private ?string $plainPassword = null;

    #[ORM\OneToMany(mappedBy: 'ownedBy', targetEntity: ApiToken::class, orphanRemoval: true)]
    private Collection $apiTokens;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $verifiedAt = null;

    #[Assert\NotBlank(groups: ['email:verify'])]
    #[Groups(['email:verify'])]
    #[SerializedName('token')]
    public ?string $verifyToken = null;

    public function __construct()
    {
        parent::__construct();
        $this->apiTokens = new ArrayCollection();
    }


    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = array_filter($roles);

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }


    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
         $this->plainPassword = null;
    }

    /**
     * @return Collection<int, ApiToken>
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    public function addApiToken(ApiToken $apiToken): self
    {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens->add($apiToken);
            $apiToken->setOwnedBy($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): self
    {
        if ($this->apiTokens->removeElement($apiToken)) {
            // set the owning side to null (unless already changed)
            if ($apiToken->getOwnedBy() === $this) {
                $apiToken->setOwnedBy(null);
            }
        }

        return $this;
    }

    public function getValidTokenStrings(): array
    {
        return $this->getApiTokens()
            ->filter(fn (ApiToken $token) => $token->isValid())
            ->map(fn (ApiToken $token) => $token->getToken())
            ->toArray();
    }

    public function getVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->verifiedAt;
    }

    public function setVerifiedAt(?\DateTimeImmutable $verifiedAt): self
    {
        $this->verifiedAt = $verifiedAt;

        return $this;
    }

    public function getVerifiedToken(): ?string
    {
        return $this->verifyToken;
    }

    public function setVerifiedToken(?string $token): self
    {
        $this->verifyToken = $token;

        return $this;
    }
}
