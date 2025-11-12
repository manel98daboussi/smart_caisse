<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TableQRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TableQRRepository::class)]
#[ApiResource]
class TableQR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length:50)]
    private ?string $numero = null;

    #[ORM\Column(length:255, nullable:true)]
    private ?string $qrCode = null;

    #[ORM\ManyToOne(inversedBy: 'tables')]
    private ?Etablissement $etablissement = null;

    #[ORM\OneToMany(mappedBy: 'table', targetEntity: CommandeTable::class)]
    private Collection $commandes;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNumero(): ?string { return $this->numero; }
    public function setNumero(string $n): self { $this->numero = $n; return $this; }

    public function getQrCode(): ?string { return $this->qrCode; }
    public function setQrCode(?string $code): self { $this->qrCode = $code; return $this; }

    public function getEtablissement(): ?Etablissement { return $this->etablissement; }
    public function setEtablissement(?Etablissement $e): self { $this->etablissement = $e; return $this; }

    /** @return Collection|CommandeTable[] */
    public function getCommandes(): Collection { return $this->commandes; }
    public function addCommande(CommandeTable $c): self {
        if (!$this->commandes->contains($c)) {
            $this->commandes->add($c);
            $c->setTable($this);
        }
        return $this;
    }
    public function removeCommande(CommandeTable $c): self {
        if ($this->commandes->removeElement($c)) {
            if ($c->getTable() === $this) {
                $c->setTable(null);
            }
        }
        return $this;
    }
}
