<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\VenteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VenteRepository::class)]
#[ApiResource]
class Vente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateVente = null;

    #[ORM\Column(type: 'float')]
    private float $totalHT = 0.0;

    #[ORM\Column(type: 'float')]
    private float $totalTTC = 0.0;

    #[ORM\Column(length:50)]
    private string $modePaiement = 'cash';

    #[ORM\ManyToOne(inversedBy: 'ventes')]
    private ?Session $session = null;

    #[ORM\ManyToOne(inversedBy: 'ventes')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'vente', targetEntity: LigneDeVente::class, cascade: ['persist', 'remove'])]
    private Collection $lignes;

    #[ORM\OneToOne(mappedBy: 'vente', targetEntity: Facture::class, cascade: ['persist', 'remove'])]
    private ?Facture $facture = null;

    public function __construct()
    {
        $this->lignes = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getDateVente(): ?\DateTimeInterface { return $this->dateVente; }
    public function setDateVente(\DateTimeInterface $dt): self { $this->dateVente = $dt; return $this; }

    public function getTotalHT(): float { return $this->totalHT; }
    public function setTotalHT(float $v): self { $this->totalHT = $v; return $this; }

    public function getTotalTTC(): float { return $this->totalTTC; }
    public function setTotalTTC(float $v): self { $this->totalTTC = $v; return $this; }

    public function getModePaiement(): string { return $this->modePaiement; }
    public function setModePaiement(string $m): self { $this->modePaiement = $m; return $this; }

    public function getSession(): ?Session { return $this->session; }
    public function setSession(?Session $s): self { $this->session = $s; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $u): self { $this->user = $u; return $this; }

    /** @return Collection|LigneDeVente[] */
    public function getLignes(): Collection { return $this->lignes; }
    public function addLigne(LigneDeVente $l): self {
        if (!$this->lignes->contains($l)) {
            $this->lignes->add($l);
            $l->setVente($this);
        }
        return $this;
    }
    public function removeLigne(LigneDeVente $l): self {
        if ($this->lignes->removeElement($l)) {
            if ($l->getVente() === $this) {
                $l->setVente(null);
            }
        }
        return $this;
    }

    public function getFacture(): ?Facture { return $this->facture; }
    public function setFacture(?Facture $f): self {
        $this->facture = $f;
        if ($f !== null && $f->getVente() !== $this) {
            $f->setVente($this);
        }
        return $this;
    }
}
