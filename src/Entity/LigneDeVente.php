<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\LigneDeVenteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneDeVenteRepository::class)]
#[ApiResource]
class LigneDeVente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $quantite = 1;

    #[ORM\Column(type: 'float')]
    private float $prixUnitaire = 0.0;

    #[ORM\ManyToOne]
    private ?Produit $produit = null;

    #[ORM\ManyToOne(inversedBy: 'lignes')]
    private ?Vente $vente = null;

    public function getId(): ?int { return $this->id; }

    public function getQuantite(): int { return $this->quantite; }
    public function setQuantite(int $q): self { $this->quantite = $q; return $this; }

    public function getPrixUnitaire(): float { return $this->prixUnitaire; }
    public function setPrixUnitaire(float $p): self { $this->prixUnitaire = $p; return $this; }

    public function getProduit(): ?Produit { return $this->produit; }
    public function setProduit(?Produit $p): self { $this->produit = $p; return $this; }

    public function getVente(): ?Vente { return $this->vente; }
    public function setVente(?Vente $v): self { $this->vente = $v; return $this; }

    public function getSousTotal(): float { return $this->quantite * $this->prixUnitaire; }
}
