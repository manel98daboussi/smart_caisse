<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\EtablissementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtablissementRepository::class)]
#[ApiResource]
class Etablissement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length:150)]
    private ?string $nom = null;

    #[ORM\Column(length:255)]
    private ?string $adresse = null;

    #[ORM\Column(length:150, nullable:true)]
    private ?string $email = null;

    #[ORM\Column(length:50, nullable:true)]
    private ?string $telephone = null;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: TableQR::class)]
    private Collection $tables;

    public function __construct()
    {
        $this->tables = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $n): self { $this->nom = $n; return $this; }

    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(string $a): self { $this->adresse = $a; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $e): self { $this->email = $e; return $this; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $t): self { $this->telephone = $t; return $this; }

    /** @return Collection|TableQR[] */
    public function getTables(): Collection { return $this->tables; }
    public function addTable(TableQR $table): self {
        if (!$this->tables->contains($table)) {
            $this->tables->add($table);
            $table->setEtablissement($this);
        }
        return $this;
    }
    public function removeTable(TableQR $table): self {
        if ($this->tables->removeElement($table)) {
            if ($table->getEtablissement() === $this) {
                $table->setEtablissement(null);
            }
        }
        return $this;
    }
}
