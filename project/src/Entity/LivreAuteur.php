<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\LivreAuteurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['livreAuteur:read']],
    forceEager: false
)]
#[ORM\Entity(repositoryClass: LivreAuteurRepository::class)]
class LivreAuteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['livreAuteur:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'livreAuteurs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['livreAuteur:read'])]
    private ?Livre $livre = null;

    #[ORM\ManyToOne(inversedBy: 'livreAuteurs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['livreAuteur:read'])]
    private ?Auteur $auteur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLivre(): ?Livre
    {
        return $this->livre;
    }

    public function setLivre(?Livre $livre): static
    {
        $this->livre = $livre;

        return $this;
    }

    public function getAuteur(): ?Auteur
    {
        return $this->auteur;
    }

    public function setAuteur(?Auteur $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }
}
