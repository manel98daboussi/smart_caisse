<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\FactureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
#[ApiResource]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length:50)]
    private ?string $numero = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateEmission = null;

    #[ORM\Column(type: 'float')]
    private float $montantTotal = 0.0;

    #[ORM\Column(length:150, nullable:true)]
    private ?string $emailClient = null;

    #[ORM\OneToOne(inversedBy: 'facture', cascade: ['persist'])]
    private ?Vente $vente = null;

    public function getId(): ?int { return $this->id; }

    public function getNumero(): ?string { return $this->numero; }
    public function setNumero(string $n): self { $this->numero = $n; return $this; }

    public function getDateEmission(): ?\DateTimeInterface { return $this->dateEmission; }
    public function setDateEmission(\DateTimeInterface $d): self { $this->dateEmission = $d; return $this; }

    public function getMontantTotal(): float { return $this->montantTotal; }
    public function setMontantTotal(float $m): self { $this->montantTotal = $m; return $this; }

    public function getEmailClient(): ?string { return $this->emailClient; }
    public function setEmailClient(?string $e): self { $this->emailClient = $e; return $this; }

    public function getVente(): ?Vente { return $this->vente; }
    public function setVente(?Vente $v): self {
        $this->vente = $v;
        if ($v !== null && $v->getFacture() !== $this) {
            $v->setFacture($this);
        }
        return $this;
    }
}
