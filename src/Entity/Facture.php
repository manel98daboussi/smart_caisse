<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\FactureRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['facture:read']],
    denormalizationContext: ['groups' => ['facture:write']],
    shortName: 'Factures',
    operations:[
        new GetCollection(
            uriTemplate:'/factures',
            order: ['id' => 'DESC'],
            openapi: new Operation(
                summary: 'Récupérer les factures',
                description:  'Récupérer les factures',
            )
            
        ),
      new Post(
            uriTemplate: '/factures',
            openapi: new Operation(
                summary: 'Ajouter une facture',
                description: 'Ajouter une facture'
            )
        ),
        new Get(
            uriTemplate: '/factures/{id}',
            openapi: new Operation(
                description: 'Récupérer une facture par ID',
                summary: 'Récupérer une facture par ID'
            )
        ),

        new Patch(
            uriTemplate: '/factures/{id}',
            openapi: new Operation(
                description: 'Modifier une facture par ID',
                summary: 'Modifier une facture par ID'
            )
        ),
        new Delete(
            uriTemplate: '/factures/{id}',
            openapi: new Operation(
                description: 'Supprimer une facture par ID',
                summary: 'Supprimer une facture par ID'
            )
        ),
    ]
)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['facture:read'])]
    private ?int $id = null;

    #[ORM\Column(length:50)]
    #[Groups(['facture:read', 'facture:write'])]
    private ?string $numero = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['facture:read', 'facture:write'])]
    private ?\DateTimeInterface $dateEmission = null;

    #[ORM\Column(type: 'float')]
    #[Groups(['facture:read', 'facture:write'])]
    private float $montantTotal = 0.0;

    #[ORM\Column(length:150, nullable:true)]
    #[Groups(['facture:read', 'facture:write'])]
    private ?string $emailClient = null;

    #[ORM\OneToOne(inversedBy: 'facture', cascade: ['persist'])]
    #[Groups(['facture:read', 'facture:write'])]
    #[ApiProperty(example:'/api/factures/1')]
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
