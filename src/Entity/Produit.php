<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProduitRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['produit:read']],
    denormalizationContext: ['groups' => ['produit:write']],
    shortName: 'Prouits',
    operations: [
        new GetCollection(
            uriTemplate: '/produits',
            order: ['id' => 'DESC'],
            openapi: new Operation(
                summary: 'Récupérer la liste des produits',
                description: 'Récupérer la liste des produits',
            )

        ),

        new Get(
            uriTemplate: '/produits/categories',
            name: 'api_produits_categories',
            openapi: new Operation(
                summary: "Récupérer les catégories des produits",
                description: "Récupérer les catégories des produits",
            )
        ),
        new Get(
            uriTemplate: '/produits/best-selling-products',
            name: 'api_produits_best_selling_products',
            openapi: new Operation(
                summary: "Récupérer les meilleurs produits",
                description: "Récupérer les meilleurs produits",
            )
        ),
        new Post(
            uriTemplate: '/produits',
            openapi: new Operation(
                summary: 'Ajouter un produit',
                description: 'Ajouter un produit'
            )
        ),
        new Get(
            uriTemplate: '/produits/{id}',
            openapi: new Operation(
                description: 'Récupérer un produit par ID',
                summary: 'Récupérer un produit par ID'
            )
        ),

        new Patch(
            uriTemplate: '/produits/{id}',
            openapi: new Operation(
                description: 'Modifier un produit par ID',
                summary: 'Modifier un produit par ID'
            )
        ),
        new Delete(
            uriTemplate: '/produits/{id}',
            openapi: new Operation(
                description: 'Supprimer un produit par ID',
                summary: 'Supprimer un produit par ID'
            )
        ),
    ]
)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['produit:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Groups(['produit:read', 'produit:write'])]
    private ?string $nom = null;

    #[ORM\Column(type: 'float')]
    #[Groups(['produit:read', 'produit:write'])]
    private float $prix = 0.0;

    #[ORM\Column(type: 'integer')]
    #[Groups(['produit:read', 'produit:write'])]
    private int $stock = 0;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['produit:read', 'produit:write'])]
    private ?string $categorie = null;

    #[ORM\Column(type: 'float')]
    #[Groups(['produit:read', 'produit:write'])]
    private float $tva = 0.0;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['produit:read', 'produit:write'])]
    private bool $actif = true;

    // Adding discount fields
    #[ORM\Column(type: 'float')]
    #[Groups(['produit:read', 'produit:write'])]
    private float $remise = 0.0;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['produit:read', 'produit:write'])]
    private ?\DateTimeInterface $dateDebutRemise = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['produit:read', 'produit:write'])]
    private ?\DateTimeInterface $dateFinRemise = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }
    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }
    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }
    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        return $this;
    }
    public function incrementStock(int $qty): self
    {
        $this->stock += $qty;
        return $this;
    }
    public function decrementStock(int $qty): self
    {
        $this->stock -= $qty;
        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }
    public function setCategorie(?string $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getTva(): float
    {
        return $this->tva;
    }
    public function setTva(float $tva): self
    {
        $this->tva = $tva;
        return $this;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }
    public function setActif(bool $actif): self
    {
        $this->actif = $actif;
        return $this;
    }

    // Discount getters and setters
    public function getRemise(): float
    {
        return $this->remise;
    }
    public function setRemise(float $remise): self
    {
        $this->remise = $remise;
        return $this;
    }

    public function getDateDebutRemise(): ?\DateTimeInterface
    {
        return $this->dateDebutRemise;
    }
    public function setDateDebutRemise(?\DateTimeInterface $dateDebutRemise): self
    {
        $this->dateDebutRemise = $dateDebutRemise;
        return $this;
    }

    public function getDateFinRemise(): ?\DateTimeInterface
    {
        return $this->dateFinRemise;
    }
    public function setDateFinRemise(?\DateTimeInterface $dateFinRemise): self
    {
        $this->dateFinRemise = $dateFinRemise;
        return $this;
    }

    // Helper method to check if product is currently discounted
    public function isEnRemise(): bool
    {
        if ($this->remise <= 0) {
            return false;
        }

        $now = new \DateTime();
        if ($this->dateDebutRemise && $now < $this->dateDebutRemise) {
            return false;
        }

        if ($this->dateFinRemise && $now > $this->dateFinRemise) {
            return false;
        }

        return true;
    }

    // Helper method to calculate discounted price
    public function getPrixRemise(): float
    {
        if ($this->isEnRemise()) {
            return $this->prix * (1 - $this->remise / 100);
        }
        return $this->prix;
    }
}
