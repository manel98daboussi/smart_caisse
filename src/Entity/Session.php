<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SessionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ApiResource]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateOuverture = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateFermeture = null;

    #[ORM\Column(type: 'float')]
    private float $montantInitial = 0.0;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $montantFinal = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?User $user = null;

    public function getId(): ?int { return $this->id; }

    public function getDateOuverture(): ?\DateTimeInterface { return $this->dateOuverture; }
    public function setDateOuverture(\DateTimeInterface $dt): self { $this->dateOuverture = $dt; return $this; }

    public function getDateFermeture(): ?\DateTimeInterface { return $this->dateFermeture; }
    public function setDateFermeture(?\DateTimeInterface $dt): self { $this->dateFermeture = $dt; return $this; }

    public function getMontantInitial(): float { return $this->montantInitial; }
    public function setMontantInitial(float $m): self { $this->montantInitial = $m; return $this; }

    public function getMontantFinal(): ?float { return $this->montantFinal; }
    public function setMontantFinal(?float $m): self { $this->montantFinal = $m; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }
}
