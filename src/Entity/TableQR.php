<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Entity\CommandeTable;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\TableQRRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TableQRRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['table_qr:read']],
    denormalizationContext: ['groups' => ['table_qr:write']],
    shortName: 'Tables QR',
    operations:[
        new GetCollection(
            uriTemplate:'/tables-qr',
            order: ['id' => 'DESC'],
            openapi: new Operation(
                summary: 'Récupérer les tables-qr',
                description:  'Récupérer les tables-qr',
            )
            
        ),
      new Post(
            uriTemplate: '/tables-qr',
            openapi: new Operation(
                summary: 'Ajouter une table-qr',
                description: 'Ajouter une table-qr'
            )
        ),
        new Get(
            uriTemplate: '/tables-qr/{id}',
            openapi: new Operation(
                description: 'Récupérer une table-qr par ID',
                summary: 'Récupérer une table-qr par ID'
            )
        ),

        new Patch(
            uriTemplate: '/tables-qr/{id}',
            openapi: new Operation(
                description: 'Modifier une table-qr par ID',
                summary: 'Modifier une table-qr par ID'
            )
        ),
        new Delete(
            uriTemplate: '/tables-qr/{id}',
            openapi: new Operation(
                description: 'Supprimer une table-qr par ID',
                summary: 'Supprimer une table-qr par ID'
            )
        ),
    ]
)]
class TableQR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['table_qr:read','etablissement:read'])]
    private ?int $id = null;

    #[ORM\Column(length:50)]
    #[Groups(['table_qr:read', 'table_qr:write','etablissement:read'])]
    private ?string $numero = null;

    #[ORM\Column(length:255, nullable:true)]
    #[Groups(['table_qr:read', 'table_qr:write','etablissement:read'])]
    private ?string $qrCode = null;

    #[ORM\ManyToOne(inversedBy: 'tables')]
    #[Groups(['table_qr:read', 'table_qr:write'])]
    #[ApiProperty(example:'/api/etablissements/1')]
    private ?Etablissement $etablissement = null;

    #[ORM\OneToMany(mappedBy: 'table', targetEntity: CommandeTable::class)]
    #[Groups(['table_qr:read'])]
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
