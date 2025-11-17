<?php

namespace App\Entity;

use App\Entity\Facture;
use App\Entity\LigneDeVente;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\VenteRepository;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VenteRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['vente:read']],
    denormalizationContext: ['groups' => ['vente:write']],
    shortName: 'Ventes',
    operations:[
        new GetCollection(
            uriTemplate:'/ventes/stats',
            name:'api_ventes_stats',
            order: ['id' => 'DESC'],
            openapi: new Operation(
                summary: 'Récupérer les statistiques des ventes',
                description: "Récupérer les statistiques des ventes",
            )
            
        ),
        new Get(
            uriTemplate:'/daily-report/{dateVente}',
            name:'api_ventes_daily_report',
            openapi: new Operation(
                summary: 'Récupérer le rapport journalier',
                description: "Récupérer le rapport journalier",
                parameters : [
                    new Parameter(
                        name: 'dateVente',
                        in: 'path',
                        description: 'Date du rapport (YYYY-MM-DD)',
                        required: true,
                        schema: [
                            'type' => 'string',
                            'format' => 'date'
                        ]
                    )
                ]
            )
        ),
        new Get(
            uriTemplate:'/history',
            name:'api_ventes_history',
            openapi: new Operation(
                summary: "Récupérer l'historique des ventes",
                description: "Récupérer l'historique des ventes",
            )
            ),
        new Post(
            uriTemplate: '/ventes',
            openapi: new Operation(
                summary: 'Ajouter une vente',
                description: 'Ajouter une vente'
            )
        ),
        new Get(
            uriTemplate: '/ventes/{id}',
            openapi: new Operation(
                description: 'Récupérer une vente par ID',
                summary: 'Récupérer une vente par ID'
            )
        ),

        new Patch(
            uriTemplate: '/ventes/{id}',
            openapi: new Operation(
                description: 'Modifier une vente par ID',
                summary: 'Modifier une vente par ID'
            )
        ),
        new Delete(
            uriTemplate: '/ventes/{id}',
            openapi: new Operation(
                description: 'Supprimer une vente par ID',
                summary: 'Supprimer une vente par ID'
            )
        ),
    ]
)]
#[HasLifecycleCallbacks]
class Vente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['vente:read','ligne_vente:read','session:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['vente:read', 'vente:write','ligne_vente:read','session:read'])]
    private ?\DateTimeInterface $dateVente = null;

    #[ORM\Column(type: 'float')]
    #[Groups(['vente:read', 'vente:write','ligne_vente:read','session:read'])]
    private float $totalHT = 0.0;

    #[ORM\Column(type: 'float')]
    #[Groups(['vente:read', 'vente:write','session:read'])]
    private float $totalTTC = 0.0;

    #[ORM\Column(length:50)]
    #[Groups(['vente:read', 'vente:write','session:read'])]
    private string $modePaiement = 'cash';

    #[ORM\ManyToOne(inversedBy: 'ventes')]
    #[Groups(['vente:read', 'vente:write'])]
    #[ApiProperty(example:'/api/sessions/1')]
    private ?Session $session = null;

    #[ORM\ManyToOne(inversedBy: 'ventes')]
    #[Groups(['vente:read', 'vente:write'])]
    #[ApiProperty(example:'/api/users/1')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'vente', targetEntity: LigneDeVente::class, cascade: ['persist', 'remove'])]
    #[Groups(['vente:read'])]
    private Collection $lignes;

    #[ORM\OneToOne(mappedBy: 'vente', targetEntity: Facture::class, cascade: ['persist', 'remove'])]
    #[Groups(['vente:read', 'vente:write'])]
    #[ApiProperty(example:'/api/factures/1')]
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
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setDateVenteValue(): void
    {
        $this->dateVente = new \DateTimeImmutable();
    }
   
}
