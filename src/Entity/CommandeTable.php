<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CommandeTableRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeTableRepository::class)]
#[ApiResource]
class CommandeTable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column(length:50)]
    private string $statut = 'en attente';

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?TableQR $table = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?User $user = null;

    public function getId(): ?int { return $this->id; }

    public function getDateCommande(): ?\DateTimeInterface { return $this->dateCommande; }
    public function setDateCommande(\DateTimeInterface $d): self { $this->dateCommande = $d; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $s): self { $this->statut = $s; return $this; }

    public function getTable(): ?TableQR { return $this->table; }
    public function setTable(?TableQR $t): self { $this->table = $t; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $u): self { $this->user = $u; return $this; }
}
