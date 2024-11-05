<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' =>  ['user:write']],
    forceEager: false
)]
#[ApiFilter(SearchFilter::class, properties: [ 'pseudo' => 'partial' ,'email' => 'exact'])]
#[UniqueEntity('email')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name:'user')]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name:'discr', type: 'string')]
#[ORM\DiscriminatorMap(['client'=>Client::class, 'user'=>User::class])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    #[Groups('user:read','client:read')]
    protected ?int $id = null;

    #[ORM\Column(type:'string', length: 50)]
    #[Assert\NotBlank()]
    #[Assert\Length(min:2,max:50)]
    #[Groups(['user:read','user:write','client:read','client:write'])]
    protected ?string $nom = null;

    #[ORM\Column(type:'string', length: 50)]
    #[Assert\Length(min:2,max:50)]
    #[Assert\NotBlank()]
    #[Groups(['user:read','user:write','client:read','client:write'])]
    protected ?string $prenom = null;

     #[ORM\Column(type:'string', length: 50, unique: true)]
     #[Assert\NotBlank()]
     #[Assert\Length(min:2,max:50)]
     #[Groups(['user:read','user:write','client:read','client:write'])]
    protected ?string $pseudo = null;

    #[ORM\Column(type:'string', length: 180, unique: true)]
    #[Assert\Email()]
    #[Assert\Length(min:2,max:180)]
    #[Assert\NotBlank()]
    #[Groups(['user:read','user:write','client:read','client:write'])]
    protected ?string $email;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type:'json')]
    #[Assert\NotNull()]
    #[Groups(['user:read','user:write','client:read','client:write'])]
    protected array $roles = [];


    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Groups(['user:read','user:write','client:read','client:write'])]
    protected ?string $password = null;


    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    #[Groups(['user:read','user:write','client:read','client:write'])]
    protected ?string $telephone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Groups(['user:read','user:write','client:read','client:write'])]
    protected ?string $photo = null;

    public function __construct()
    {
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }
    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_CLIENT
        $roles[] = 'ROLE_CLIENT';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }


    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }
}
