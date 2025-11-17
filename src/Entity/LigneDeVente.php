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
use ApiPlatform\OpenApi\Model\Parameter;
use App\Repository\LigneDeVenteRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LigneDeVenteRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['ligne_vente:read']],
    denormalizationContext: ['groups' => ['ligne_vente:write']],
    shortName: 'Lignes de Ventes',
    operations: [
        new GetCollection(
            uriTemplate: '/lignes-ventes',
            order: ['id' => 'DESC'],
            openapi: new Operation(
                summary: 'Récupérer la liste des lignes de ventes',
                description: 'Récupérer la liste des lignes de ventes',
            )

        ),
        new Post(
            uriTemplate: '/lignes-ventes',
            openapi: new Operation(
                summary: 'Ajouter une ligne de vente',
                description: 'Ajouter une ligne de vente'
            )
        ),
        new Get(
            uriTemplate: '/lignes-ventes/{id}',
            openapi: new Operation(
                description: 'Récupérer une ligne de vente par ID',
                summary: 'Récupérer une ligne de vente par ID'
            )
        ),

        new Patch(
            uriTemplate: '/lignes-ventes/{id}',
            openapi: new Operation(
                description: 'Modifier une ligne de vente par ID',
                summary: 'Modifier une ligne de vente par ID'
            )
        ),
        new Delete(
            uriTemplate: '/lignes-ventes/{id}',
            openapi: new Operation(
                description: 'Supprimer une ligne de vente par ID',
                summary: 'Supprimer une ligne de vente par ID'
            )
        ),
    ]
)]
class LigneDeVente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['vente:read','ligne_vente:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['vente:read','ligne_vente:read','ligne_vente:write'])]
    private int $quantite = 1;

    #[ORM\Column(type: 'float')]
    #[Groups(['vente:read','ligne_vente:read','ligne_vente:write'])]
    private float $prixUnitaire = 0.0;

    #[ORM\ManyToOne]
    #[Groups(['ligne_vente:read','ligne_vente:write'])]
    #[ApiProperty(example:'/api/produits/1')]
    private ?Produit $produit = null;

    #[ORM\ManyToOne(inversedBy: 'lignes')]
    #[Groups(['ligne_vente:read','ligne_vente:write'])]
    #[ApiProperty(example:'/api/ventes/1')]
    private ?Vente $vente = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }
    public function setQuantite(int $q): self
    {
        $this->quantite = $q;
        return $this;
    }

    public function getPrixUnitaire(): float
    {
        return $this->prixUnitaire;
    }
    public function setPrixUnitaire(float $p): self
    {
        $this->prixUnitaire = $p;
        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }
    public function setProduit(?Produit $p): self
    {
        $this->produit = $p;
        return $this;
    }

    public function getVente(): ?Vente
    {
        return $this->vente;
    }
    public function setVente(?Vente $v): self
    {
        $this->vente = $v;
        return $this;
    }

    public function getSousTotal(): float
    {
        return $this->quantite * $this->prixUnitaire;
    }
}
