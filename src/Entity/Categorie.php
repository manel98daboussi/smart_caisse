<?php

namespace App\Entity;

use App\Entity\Produit;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\CategorieRepository;
use ApiPlatform\OpenApi\Model\Operation;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['categorie:read']],
    denormalizationContext: ['groups' => ['categorie:write']],
    shortName: 'Catégories',
    operations:[
        new GetCollection(
            uriTemplate:'/categories',
            order: ['id' => 'DESC'],
            openapi: new Operation(
                summary: 'Récupérer les factures',
                description:  'Récupérer les factures',
            )
            
        ),
      new Post(
            uriTemplate: '/categories',
            openapi: new Operation(
                summary: 'Ajouter une categorie',
                description: 'Ajouter une categorie'
            )
        ),
        new Get(
            uriTemplate: '/categories/{id}',
            openapi: new Operation(
                description: 'Récupérer une categorie par ID',
                summary: 'Récupérer une categorie par ID'
            )
        ),

        new Patch(
            uriTemplate: '/categories/{id}',
            openapi: new Operation(
                description: 'Modifier une categorie par ID',
                summary: 'Modifier une categorie par ID'
            )
        ),
        new Delete(
            uriTemplate: '/categories/{id}',
            openapi: new Operation(
                description: 'Supprimer une categorie par ID',
                summary: 'Supprimer une categorie par ID'
            )
        ),
    ]
)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['categorie:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Groups(['categorie:read', 'categorie:write'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['categorie:read', 'categorie:write'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['categorie:read', 'categorie:write'])]
    private ?int $ordre = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'categorie')]
    #[Groups(['categorie:read'])]
    #[ApiProperty(example:'/api/categories/1')]
    private Collection $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): static
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->setCategorie($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getCategorie() === $this) {
                $produit->setCategorie(null);
            }
        }

        return $this;
    }
}
