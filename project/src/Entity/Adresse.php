<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\AdresseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['adresse:read','user:read']],
    denormalizationContext: ['groups' => 'user:write'],
    forceEager: false
)]
#[ApiFilter(SearchFilter::class, properties: [ 'ville' => 'partial' ,'codePostal' => 'exact'])]
#[ORM\Entity(repositoryClass: AdresseRepository::class)]
class Adresse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['client:read','client:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['client:read','client:write'])]
    private ?string $rue = null;

    #[ORM\Column(length: 50)]
    #[Groups(['client:read','client:write'])]
    private ?string $ville = null;

    #[ORM\Column(length: 50)]
    #[Groups(['client:read','client:write'])]
    private ?string $codePostal = null;

    #[ORM\Column(length: 100)]
    #[Groups(['client:read','client:write'])]
    private ?string $pays = null;

    #[ORM\ManyToOne(inversedBy: 'adresses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:read','user:write'])]
    private ?Client $client = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): static
    {
        $this->rue = $rue;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->id;
    }
}
