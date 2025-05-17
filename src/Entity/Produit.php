<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomProduit = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column]
    private ?int $quantiteStock = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateAjout = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Approvisionnement::class)]
    private Collection $approvisionnements;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: VenteProduit::class)]
    private Collection $venteProduits;

    public function __construct()
    {
        $this->approvisionnements = new ArrayCollection();
        $this->venteProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProduit(): ?string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(string $nomProduit): self
    {
        $this->nomProduit = $nomProduit;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getQuantiteStock(): ?int
    {
        return $this->quantiteStock;
    }

    public function setQuantiteStock(int $quantiteStock): self
    {
        $this->quantiteStock = $quantiteStock;

        return $this;
    }

    public function getDateAjout(): ?\DateTimeImmutable
    {
        return $this->dateAjout;
    }

    public function setDateAjout(\DateTimeImmutable $dateAjout): self
    {
        $this->dateAjout = $dateAjout;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Approvisionnement>
     */
    public function getApprovisionnements(): Collection
    {
        return $this->approvisionnements;
    }

    public function addApprovisionnement(Approvisionnement $approvisionnement): self
    {
        if (!$this->approvisionnements->contains($approvisionnement)) {
            $this->approvisionnements->add($approvisionnement);
            $approvisionnement->setProduit($this);
        }

        return $this;
    }

    public function removeApprovisionnement(Approvisionnement $approvisionnement): self
    {
        if ($this->approvisionnements->removeElement($approvisionnement)) {
            // set the owning side to null (unless already changed)
            if ($approvisionnement->getProduit() === $this) {
                $approvisionnement->setProduit(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->nomProduit;
    }

    /**
     * @return Collection<int, VenteProduit>
     */
    public function getVenteProduits(): Collection
    {
        return $this->venteProduits;
    }

    public function addVenteProduit(VenteProduit $venteProduit): self
    {
        if (!$this->venteProduits->contains($venteProduit)) {
            $this->venteProduits->add($venteProduit);
            $venteProduit->setProduit($this);
        }

        return $this;
    }

    public function removeVenteProduit(VenteProduit $venteProduit): self
    {
        if ($this->venteProduits->removeElement($venteProduit)) {
            // set the owning side to null (unless already changed)
            if ($venteProduit->getProduit() === $this) {
                $venteProduit->setProduit(null);
            }
        }

        return $this;
    }

}
