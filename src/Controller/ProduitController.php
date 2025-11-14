<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/produits')]
class ProduitController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ProduitRepository $produitRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProduitRepository $produitRepository
    ) {
        $this->entityManager = $entityManager;
        $this->produitRepository = $produitRepository;
    }

    #[Route('', name: 'produit_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $categorie = $request->query->get('categorie');
        $actif = $request->query->get('actif');

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p')
           ->from(Produit::class, 'p');

        if ($categorie) {
            $qb->andWhere('p.categorie = :categorie')
               ->setParameter('categorie', $categorie);
        }

        if ($actif !== null) {
            $qb->andWhere('p.actif = :actif')
               ->setParameter('actif', filter_var($actif, FILTER_VALIDATE_BOOLEAN));
        }

        $qb->orderBy('p.nom', 'ASC');

        $produits = $qb->getQuery()->getResult();

        return $this->json($produits);
    }

    #[Route('/{id}', name: 'produit_get', methods: ['GET'])]
    public function get(Produit $produit): JsonResponse
    {
        return $this->json($produit);
    }

    #[Route('', name: 'produit_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $produit = new Produit();
        $produit->setNom($data['nom']);
        $produit->setPrix(floatval($data['prix']));
        $produit->setStock(intval($data['stock']));
        $produit->setCategorie($data['categorie'] ?? null);
        $produit->setTva(floatval($data['tva'] ?? 0));
        $produit->setActif(boolval($data['actif'] ?? true));
        $produit->setRemise(floatval($data['remise'] ?? 0));

        if (isset($data['dateDebutRemise'])) {
            $produit->setDateDebutRemise(new \DateTime($data['dateDebutRemise']));
        }

        if (isset($data['dateFinRemise'])) {
            $produit->setDateFinRemise(new \DateTime($data['dateFinRemise']));
        }

        $this->entityManager->persist($produit);
        $this->entityManager->flush();

        return $this->json($produit, 201);
    }

    #[Route('/{id}', name: 'produit_update', methods: ['PUT'])]
    public function update(Produit $produit, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) {
            $produit->setNom($data['nom']);
        }

        if (isset($data['prix'])) {
            $produit->setPrix(floatval($data['prix']));
        }

        if (isset($data['stock'])) {
            $produit->setStock(intval($data['stock']));
        }

        if (array_key_exists('categorie', $data)) {
            $produit->setCategorie($data['categorie']);
        }

        if (isset($data['tva'])) {
            $produit->setTva(floatval($data['tva']));
        }

        if (isset($data['actif'])) {
            $produit->setActif(boolval($data['actif']));
        }

        if (isset($data['remise'])) {
            $produit->setRemise(floatval($data['remise']));
        }

        if (array_key_exists('dateDebutRemise', $data)) {
            $produit->setDateDebutRemise($data['dateDebutRemise'] ? new \DateTime($data['dateDebutRemise']) : null);
        }

        if (array_key_exists('dateFinRemise', $data)) {
            $produit->setDateFinRemise($data['dateFinRemise'] ? new \DateTime($data['dateFinRemise']) : null);
        }

        $this->entityManager->flush();

        return $this->json($produit);
    }

    #[Route('/{id}', name: 'produit_delete', methods: ['DELETE'])]
    public function delete(Produit $produit): JsonResponse
    {
        $this->entityManager->remove($produit);
        $this->entityManager->flush();

        return $this->json(['message' => 'Product deleted successfully']);
    }

    #[Route('/categories', name: 'produit_categories', methods: ['GET'])]
    public function categories(): JsonResponse
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('DISTINCT p.categorie')
           ->from(Produit::class, 'p')
           ->where('p.categorie IS NOT NULL')
           ->andWhere('p.categorie != \'\'')
           ->orderBy('p.categorie', 'ASC');

        $categories = $qb->getQuery()->getSingleColumnResult();

        return $this->json($categories);
    }
}