<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ApiResource(
    normalizationContext: ['groups' => ['commande:read','statusCommande:read']],
    denormalizationContext: ['groups' => 'commande:write', 'commande:update'],
    forceEager: false
)]
#[ApiFilter(SearchFilter::class, properties: ['commandeReference' => 'exact'])]
#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['client:read','commande:read','statusCommande:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['client:read','commande:read','statusCommande:read'])]
    private ?string $commandeReference = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['client:read','commande:read','statusCommande:read'])]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column]
    #[Groups(['client:read','commande:read','statusCommande:read'])]
    private ?float $total = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn()]
    #[MaxDepth(1)]
    #[Groups(['commande:read','statusCommande:read'])]
    private ?Client $client;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['commande:read'])]
    #[MaxDepth(1)]
    private ?StatusCommande $statusCommande = null;


    /**
     * @var Collection<int, LivreCommande>
     */
    #[ORM\OneToMany(targetEntity: LivreCommande::class, mappedBy: 'commande', cascade: ["persist"], orphanRemoval: true)]
    private Collection $livreCommandes;

    public function __construct()
    {
        $this->livreCommandes = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommandeReference(): ?string
    {
        return $this->commandeReference;
    }

    public function setCommandeReference(string $commandeReference): static
    {
        $this->commandeReference = $commandeReference;

        return $this;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): static
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getStatusCommande(): ?StatusCommande
    {
        return $this->statusCommande;
    }

    public function setStatusCommande(?StatusCommande $statusCommande): static
    {
        $this->statusCommande = $statusCommande;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getLivres(): Collection
    {
        return $this->getLivreCommandes()->map(function (LivreCommande $livreCommande) {
            return $livreCommande->getLivre();
        });
    }
    public function addLivre(Livre $livre, int $quantite): static
    {
        foreach ($this->livreCommandes as $existingCommande) {
            if ($existingCommande->getLivre()->getId() === $livre->getId()) {
                return $this;
            }
        }

        // If the commande doesn't exist in the collection, add it
        $livreCommande = new LivreCommande();
        $livreCommande->setCommande($this);
        $livreCommande->setLivre($livre);
        $livreCommande->setQuantite($quantite);

        $this->livreCommandes->add($livreCommande);


        return $this;
    }

    public function removeLivre(Livre $livre): static
    {
        foreach ($this->livreCommandes as $key => $livreCommande) {
            if ($livreCommande->getLivre() === $livre) {
                $this->livreCommandes->removeElement($livreCommande);
                $livre->removeCommande($this);
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
            $livreCommande->setCommande($this);
        }

        return $this;
    }

    public function removeLivreCommande(LivreCommande $livreCommande): static
    {
        if ($this->livreCommandes->removeElement($livreCommande)) {
            // set the owning side to null (unless already changed)
            if ($livreCommande->getCommande() === $this) {
                $livreCommande->setCommande(null);
            }
        }

        return $this;
    }
}
