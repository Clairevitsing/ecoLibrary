<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\AuteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['auteur:read','livre:read']],
    denormalizationContext: ['groups' => 'livre:write', 'auteur:write'],
    forceEager: false
)]
#[ApiFilter(SearchFilter::class, properties: [ 'nom' => 'exact' ,'prenom' => 'exact'])]
#[ORM\Entity(repositoryClass: AuteurRepository::class)]
class Auteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['livre:read','auteur:read','livreAuteur:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['livre:read','auteur:read','livreAuteur:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Groups(['livre:read','auteur:read','livreAuteur:read'])]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['livre:read','auteur:read'])]
    private ?string $biographie = null;

    /**
     * @var Collection<int, LivreAuteur>
     */
    #[ORM\OneToMany(targetEntity: LivreAuteur::class, mappedBy: 'auteur',  cascade: ["persist"],orphanRemoval: true)]
    private Collection $livreAuteurs;

    public function __construct()
    {
        $this->livreAuteurs = new ArrayCollection();
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

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(string $biographie): static
    {
        $this->biographie = $biographie;

        return $this;
    }

    /**
     * @return Collection<int, Livre>
     */
    public function getLivres(): Collection
    {
        return $this->getLivreAuteurs()->map(function (LivreAuteur $livreAuteur) {
            return $livreAuteur->getLivre();
        });
    }

    public function addLivre(livre $livre): static
    {

        foreach ($this->livreAuteurs as $existingLivre) {
            if ($existingLivre->getLivre()->getId() === $livre->getId()) {
                return $this;
            }
        }

        // If the commande doesn't exist in the collection, add it
        $livreAuteur = new LivreAuteur();
        $livreAuteur->setAuteur($this);
        $livreAuteur->setLivre($livre);

        $this->livreAuteurs->add($livreAuteur);
        return $this;
    }

    public function removeLivre(Livre $livre): static
    {
        if ($this->livres->removeElement($livre)) {
            $livre->removeAuteur($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, LivreAuteur>
     */
    public function getLivreAuteurs(): Collection
    {
        return $this->livreAuteurs;
    }

    public function addLivreAuteur(LivreAuteur $livreAuteur): static
    {
        if (!$this->livreAuteurs->contains($livreAuteur)) {
            $this->livreAuteurs->add($livreAuteur);
            $livreAuteur->setAuteur($this);
        }

        return $this;
    }

    public function removeLivreAuteur(LivreAuteur $livreAuteur): static
    {
        if ($this->livreAuteurs->removeElement($livreAuteur)) {
            // set the owning side to null (unless already changed)
            if ($livreAuteur->getAuteur() === $this) {
                $livreAuteur->setAuteur(null);
            }
        }

        return $this;
    }
}
