<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ApiResource]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length:150)]
    private ?string $nom = null;

    #[ORM\Column(type: 'float')]
    private float $prix = 0.0;

    #[ORM\Column(type: 'integer')]
    private int $stock = 0;

    #[ORM\Column(length:100, nullable: true)]
    private ?string $categorie = null;

    #[ORM\Column(type: 'float')]
    private float $tva = 0.0;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    // Adding discount fields
    #[ORM\Column(type: 'float')]
    private float $remise = 0.0;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateDebutRemise = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateFinRemise = null;

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getPrix(): float { return $this->prix; }
    public function setPrix(float $prix): self { $this->prix = $prix; return $this; }

    public function getStock(): int { return $this->stock; }
    public function setStock(int $stock): self { $this->stock = $stock; return $this; }
    public function incrementStock(int $qty): self { $this->stock += $qty; return $this; }
    public function decrementStock(int $qty): self { $this->stock -= $qty; return $this; }

    public function getCategorie(): ?string { return $this->categorie; }
    public function setCategorie(?string $categorie): self { $this->categorie = $categorie; return $this; }

    public function getTva(): float { return $this->tva; }
    public function setTva(float $tva): self { $this->tva = $tva; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): self { $this->actif = $actif; return $this; }

    // Discount getters and setters
    public function getRemise(): float { return $this->remise; }
    public function setRemise(float $remise): self { $this->remise = $remise; return $this; }

    public function getDateDebutRemise(): ?\DateTimeInterface { return $this->dateDebutRemise; }
    public function setDateDebutRemise(?\DateTimeInterface $dateDebutRemise): self { $this->dateDebutRemise = $dateDebutRemise; return $this; }

    public function getDateFinRemise(): ?\DateTimeInterface { return $this->dateFinRemise; }
    public function setDateFinRemise(?\DateTimeInterface $dateFinRemise): self { $this->dateFinRemise = $dateFinRemise; return $this; }

    // Helper method to check if product is currently discounted
    public function isEnRemise(): bool {
        if ($this->remise <= 0) {
            return false;
        }
        
        $now = new \DateTime();
        if ($this->dateDebutRemise && $now < $this->dateDebutRemise) {
            return false;
        }
        
        if ($this->dateFinRemise && $now > $this->dateFinRemise) {
            return false;
        }
        
        return true;
    }

    // Helper method to calculate discounted price
    public function getPrixRemise(): float {
        if ($this->isEnRemise()) {
            return $this->prix * (1 - $this->remise / 100);
        }
        return $this->prix;
    }
}