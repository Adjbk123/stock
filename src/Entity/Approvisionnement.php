<?php

namespace App\Entity;

use App\Repository\ApprovisionnementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApprovisionnementRepository::class)]
class Approvisionnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'approvisionnements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateAppro = null;

    #[ORM\Column]
    private ?float $coutUnit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getDateAppro(): ?\DateTimeImmutable
    {
        return $this->dateAppro;
    }

    public function setDateAppro(\DateTimeImmutable $dateAppro): self
    {
        $this->dateAppro = $dateAppro;

        return $this;
    }

    public function getCoutUnit(): ?float
    {
        return $this->coutUnit;
    }

    public function setCoutUnit(float $coutUnit): self
    {
        $this->coutUnit = $coutUnit;

        return $this;
    }
}
