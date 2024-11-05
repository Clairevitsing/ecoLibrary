<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\LivreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(
    normalizationContext: ['groups' => ['livre:read','categorie:read','auteur:read']],
    denormalizationContext: ['groups' => 'livre:write', 'livre:update'],
    forceEager: false
)]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'titre' => 'partial','description' => 'partial'])]
#[ORM\Entity(repositoryClass: LivreRepository::class)]
class Livre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['livre:read','categorie:read','auteur:read','livreAuteur:read','livreCommande:read'])]
    #[ORM\Column]

    private ?int $id = null;
    #[Groups(['livre:read','categorie:read','auteur:read','livreAuteur:read','livreCommande:read'])]
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[Groups(['livre:read','categorie:read','auteur:read'])]
    #[ORM\Column(type:'string',length: 100)]
    private ?string $ISBN = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['livre:read','categorie:read','auteur:read'])]
    private ?string $description = null;

    #[ORM\Column(type:'string',length: 255)]
    #[Groups(['livre:read','categorie:read','auteur:read'])]
    private ?string $image = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['livre:read','categorie:read','auteur:read'])]
    private ?\DateTimeInterface $datePublication = null;

    #[ORM\Column(type:'float',length: 255)]
    #[Groups(['livre:read','categorie:read','auteur:read','livreCommande:read'])]
    private ?float $prix = null;

    #[ORM\Column(type:'integer', length: 255)]
    #[Groups(['livre:read','categorie:read','auteur:read'])]
    private ?int $stock = null;

    #[ORM\ManyToOne(inversedBy: 'livres')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['livre:read'])]
    private ?Categorie $categorie = null;

    /**
     * @var Collection<int, LivreCommande>
     */
    #[ORM\OneToMany(targetEntity: LivreCommande::class, mappedBy: 'livre', cascade: ["persist"], orphanRemoval: true)]
    private Collection $livreCommandes;

    /**
     * @var Collection<int, LivreAuteur>
     */
    #[ORM\OneToMany(targetEntity: LivreAuteur::class, mappedBy: 'livre',  cascade: ["persist"],orphanRemoval: true)]
    private Collection $livreAuteurs;

    #[ORM\Column]
    #[Groups(['livre:read','categorie:read','auteur:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['livre:read','categorie:read','auteur:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    #[Groups(['livre:read','categorie:read','auteur:read'])]
    private ?bool $isFavorited = false;

    public function __construct()
    {
        $this->livreCommandes = new ArrayCollection();
        $this->livreAuteurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getISBN(): ?string
    {
        return $this->ISBN;
    }

    public function setISBN(string $ISBN): static
    {
        $this->ISBN = $ISBN;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTimeInterface $datePublication): static
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }


    public function addAuteur(Auteur $auteur): static
    {
        foreach ($this->livreAuteurs as $existingAuteur) {
            if ($existingAuteur->getAuteur()->getId() === $auteur->getId()) {
                return $this;
            }
        }

        // If the commande doesn't exist in the collection, add it
        $livreAuteur = new LivreAuteur();
        $livreAuteur->setLivre($this);
        $livreAuteur->setAuteur($auteur);

        $this->livreAuteurs->add($livreAuteur);
        return $this;
    }

    public function removeAuteur(Auteur $auteur): static
    {
        foreach ($this->livreAuteurs as $key => $livreAuteur) {
            if ($livreAuteur->getAuteur() === $auteur) {
                $this->livreAuteurs->removeElement($livreAuteur);
                $auteur->removeLivre($this);
                break;
            }
        }
        return $this;
    }


    public function addCommande(Commande $commande, int $quantite): static
    {
        foreach ($this->livreCommandes as $existingCommande) {
            if ($existingCommande->getCommande()->getId() === $commande->getId()) {
                return $this;
            }
        }

        // If the commande doesn't exist in the collection, add it
        $livreCommande = new LivreCommande();
        $livreCommande->setLivre($this);
        $livreCommande->setCommande($commande);
        $livreCommande->setQuantite($quantite);

        $this->livreCommandes->add($livreCommande);
        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        foreach ($this->livreCommandes as $key => $livreCommande) {
            if ($livreCommande->getCommande() === $commande) {
                $this->livreCommandes->removeElement($livreCommande);
                $commande->removeLivre($this);
                break;
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LivreCommande>
     */
    public function getLivreCommandes(): Collection
    {
        return $this->livreCommandes;
    }

    public function addLivreCommande(LivreCommande $livreCommande): static
    {
        if (!$this->livreCommandes->contains($livreCommande)) {
            $this->livreCommandes->add($livreCommande);
            $livreCommande->setLivre($this);
        }

        return $this;
    }

    public function removeLivreCommande(LivreCommande $livreCommande): static
    {
        if ($this->livreCommandes->removeElement($livreCommande)) {
            // set the owning side to null (unless already changed)
            if ($livreCommande->getLivre() === $this) {
                $livreCommande->setLivre(null);
            }
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
            $livreAuteur->setLivre($this);
        }

        return $this;
    }

    public function removeLivreAuteur(LivreAuteur $livreAuteur): static
    {
        if ($this->livreAuteurs->removeElement($livreAuteur)) {
            // set the owning side to null (unless already changed)
            if ($livreAuteur->getLivre() === $this) {
                $livreAuteur->setLivre(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isFavorited(): ?bool
    {
        return $this->isFavorited;
    }

    public function setFavorited(bool $isFavorited): static
    {
        $this->isFavorited = $isFavorited;

        return $this;
    }
}
