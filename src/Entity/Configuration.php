<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use App\Repository\ConfigurationRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ConfigurationRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['config:read']],
    denormalizationContext: ['groups' => ['config:write']],
    shortName: 'Configurations',
    operations:[
        new GetCollection(
            uriTemplate:'/configs',
            order: ['id' => 'DESC'],
            openapi: new Operation(
                summary: 'Récupérer les factures',
                description:  'Récupérer les factures',
            )
            
        ),
      new Post(
            uriTemplate: '/configs',
            openapi: new Operation(
                summary: 'Ajouter une configuration',
                description: 'Ajouter une configuration'
            )
        ),
        new Get(
            uriTemplate: '/configs/{id}',
            openapi: new Operation(
                description: 'Récupérer une configuration par ID',
                summary: 'Récupérer une configuration par ID'
            )
        ),

        new Patch(
            uriTemplate: '/configs/{id}',
            openapi: new Operation(
                description: 'Modifier une configuration par ID',
                summary: 'Modifier une configuration par ID'
            )
        ),
        new Delete(
            uriTemplate: '/configs/{id}',
            openapi: new Operation(
                description: 'Supprimer une configuration par ID',
                summary: 'Supprimer une configuration par ID'
            )
        ),
    ]
)]
class Configuration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['config:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['config:read', 'config:write'])]
    private string $nomEntreprise = '';

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['config:read', 'config:write'])]
    private ?string $logoUrl = null;

    #[ORM\Column(type: 'float')]
    #[Groups(['config:read', 'config:write'])]
    private float $tauxTva = 0.0;

    #[ORM\Column(length: 10)]
    #[Groups(['config:read', 'config:write'])]
    private string $devise = 'EUR';

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['config:read', 'config:write'])]
    private ?string $symboleDevise = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['config:read', 'config:write'])]
    private bool $impressionActive = false;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['config:read', 'config:write'])]
    private ?string $typeImprimante = null; // 'bluetooth', 'usb', 'network', null

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['config:read', 'config:write'])]
    private ?string $adresseImprimante = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['config:read', 'config:write'])]
    private int $frequenceSync = 30; // seconds

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['config:read', 'config:write'])]
    private ?string $urlServeurSync = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['config:read', 'config:write'])]
    private ?string $apiKey = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEntreprise(): string
    {
        return $this->nomEntreprise;
    }

    public function setNomEntreprise(string $nomEntreprise): self
    {
        $this->nomEntreprise = $nomEntreprise;
        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(?string $logoUrl): self
    {
        $this->logoUrl = $logoUrl;
        return $this;
    }

    public function getTauxTva(): float
    {
        return $this->tauxTva;
    }

    public function setTauxTva(float $tauxTva): self
    {
        $this->tauxTva = $tauxTva;
        return $this;
    }

    public function getDevise(): string
    {
        return $this->devise;
    }

    public function setDevise(string $devise): self
    {
        $this->devise = $devise;
        return $this;
    }

    public function getSymboleDevise(): ?string
    {
        return $this->symboleDevise;
    }

    public function setSymboleDevise(?string $symboleDevise): self
    {
        $this->symboleDevise = $symboleDevise;
        return $this;
    }

    public function isImpressionActive(): bool
    {
        return $this->impressionActive;
    }

    public function setImpressionActive(bool $impressionActive): self
    {
        $this->impressionActive = $impressionActive;
        return $this;
    }

    public function getTypeImprimante(): ?string
    {
        return $this->typeImprimante;
    }

    public function setTypeImprimante(?string $typeImprimante): self
    {
        $this->typeImprimante = $typeImprimante;
        return $this;
    }

    public function getAdresseImprimante(): ?string
    {
        return $this->adresseImprimante;
    }

    public function setAdresseImprimante(?string $adresseImprimante): self
    {
        $this->adresseImprimante = $adresseImprimante;
        return $this;
    }

    public function getFrequenceSync(): int
    {
        return $this->frequenceSync;
    }

    public function setFrequenceSync(int $frequenceSync): self
    {
        $this->frequenceSync = $frequenceSync;
        return $this;
    }

    public function getUrlServeurSync(): ?string
    {
        return $this->urlServeurSync;
    }

    public function setUrlServeurSync(?string $urlServeurSync): self
    {
        $this->urlServeurSync = $urlServeurSync;
        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }
}