<?php

namespace App\Entity;

use App\Entity\TableQR;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use App\Repository\EtablissementRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EtablissementRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['etablissement:read']],
    denormalizationContext: ['groups' => ['etablissement:write']],
    shortName: 'Etablissements',
    operations:[
        new GetCollection(
            uriTemplate:'/etablissements',
            order: ['id' => 'DESC'],
            openapi: new Operation(
                summary: 'Récupérer les établissements',
                description:  'Récupérer les établissements',
            )
            
        ),
      new Post(
            uriTemplate: '/etablissements',
            openapi: new Operation(
                summary: 'Ajouter une établissement',
                description: 'Ajouter une établissement'
            )
        ),
        new Get(
            uriTemplate: '/etablissements/{id}',
            openapi: new Operation(
                description: 'Récupérer une établissement par ID',
                summary: 'Récupérer une établissement par ID'
            )
        ),

        new Patch(
            uriTemplate: '/etablissements/{id}',
            openapi: new Operation(
                description: 'Modifier une établissement par ID',
                summary: 'Modifier une établissement par ID'
            )
        ),
        new Delete(
            uriTemplate: '/etablissements/{id}',
            openapi: new Operation(
                description: 'Supprimer une établissement par ID',
                summary: 'Supprimer une établissement par ID'
            )
        ),
    ]
)]
class Etablissement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['etablissement:read'])]
    private ?int $id = null;

    #[ORM\Column(length:150)]
    #[Groups(['etablissement:read', 'etablissement:write'])]
    private ?string $nom = null;

    #[ORM\Column(length:255)]
    #[Groups(['etablissement:read', 'etablissement:write'])]
    private ?string $adresse = null;

    #[ORM\Column(length:150, nullable:true)]
    #[Groups(['etablissement:read', 'etablissement:write'])]
    private ?string $email = null;

    #[ORM\Column(length:50, nullable:true)]
    #[Groups(['etablissement:read', 'etablissement:write'])]
    private ?string $telephone = null;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: TableQR::class)]
    #[Groups(['etablissement:read'])]
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
