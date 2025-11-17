<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use App\Repository\CommandeTableRepository;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommandeTableRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['commande:read']],
    denormalizationContext: ['groups' => ['commande:write']],
    shortName: 'Commandes',
    operations:[
        new GetCollection(
            uriTemplate:'/commandes',
            order: ['id' => 'DESC'],
            openapi: new Operation(
                summary: 'Récupérer les commandes',
                description:  'Récupérer les commandes',
            )
            
        ),
      new Post(
            uriTemplate: '/commandes',
            openapi: new Operation(
                summary: 'Ajouter une commande',
                description: 'Ajouter une commande'
            )
        ),
        new Get(
            uriTemplate: '/commandes/{id}',
            openapi: new Operation(
                description: 'Récupérer une commande par ID',
                summary: 'Récupérer une commande par ID'
            )
        ),

        new Patch(
            uriTemplate: '/commandes/{id}',
            openapi: new Operation(
                description: 'Modifier une commande par ID',
                summary: 'Modifier une commande par ID'
            )
        ),
        new Delete(
            uriTemplate: '/commandes/{id}',
            openapi: new Operation(
                description: 'Supprimer une commande par ID',
                summary: 'Supprimer une commande par ID'
            )
        ),
    ]
)]
#[HasLifecycleCallbacks]
class CommandeTable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['commande:read','table_qr:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['commande:read','table_qr:read'])]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column(length:50)]
    #[Groups(['commande:read', 'commande:write','table_qr:read'])]
    private string $statut = 'en attente';

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[Groups(['commande:read', 'commande:write'])]
    #[ApiProperty(example:'/api/tables_qr/1')]
    private ?TableQR $table = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[Groups(['commande:read', 'commande:write','table_qr:read'])]
    #[ApiProperty(example:'/api/users/1')]
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
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setDateVenteValue(): void
    {
        $this->dateCommande = new \DateTimeImmutable();
    }
}
