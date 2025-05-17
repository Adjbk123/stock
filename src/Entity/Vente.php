<?php

namespace App\Entity;

use App\Repository\VenteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VenteRepository::class)]
class Vente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ventes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'ventes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column]
    private ?float $montantTotal = null;


    #[ORM\OneToMany(mappedBy: 'vente', targetEntity: VenteProduit::class)]
    private Collection $venteProduits;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateVente = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heureVente = null;

    public function __construct()
    {
        $this->venteProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

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

    public function getMontantTotal(): ?float
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(float $montantTotal): self
    {
        $this->montantTotal = $montantTotal;

        return $this;
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
            $venteProduit->setVente($this);
        }

        return $this;
    }

    public function removeVenteProduit(VenteProduit $venteProduit): self
    {
        if ($this->venteProduits->removeElement($venteProduit)) {
            // set the owning side to null (unless already changed)
            if ($venteProduit->getVente() === $this) {
                $venteProduit->setVente(null);
            }
        }

        return $this;
    }

    public function getDateVente(): ?\DateTimeInterface
    {
        return $this->dateVente;
    }

    public function setDateVente(\DateTimeInterface $dateVente): self
    {
        $this->dateVente = $dateVente;

        return $this;
    }

    public function getHeureVente(): ?\DateTimeInterface
    {
        return $this->heureVente;
    }

    public function setHeureVente(\DateTimeInterface $heureVente): self
    {
        $this->heureVente = $heureVente;

        return $this;
    }
}
