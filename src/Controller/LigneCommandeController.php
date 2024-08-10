<?php

namespace App\Controller;

use App\Entity\LigneCommande;
use App\Entity\Commande;
use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class LigneCommandeController extends AbstractController
{
    #[Route('/lignecommande', name: 'create_lignecommande', methods: ['POST'])]
    public function createLigneCommande(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['commande_id'], $data['produit'], $data['quantite'], $data['prix_unitaire'])) {
            return $this->json(['error' => 'Tous les champs sont requis'], 400);
        }

        // Récupérer la commande
        $commande = $em->getRepository(Commande::class)->find($data['commande_id']);
        if (!$commande) {
            return $this->json(['error' => 'Commande non trouvée'], 404);
        }

        $ligneCommande = new LigneCommande();
        $ligneCommande->setCommande($commande)
                      ->setProduit($data['produit'])
                      ->setQuantite($data['quantite'])
                      ->setPrixUnitaire($data['prix_unitaire']);

        $em->persist($ligneCommande);
        $em->flush();

        return $this->json($ligneCommande, 201);
    }

    #[Route('/lignecommande', name: 'get_lignecommandes', methods: ['GET'])]
    public function getLigneCommandes(LigneCommandeRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $ligneCommandes = $repository->findAll();
        $json = $serializer->serialize($ligneCommandes, 'json', ['groups' => 'default']);
        
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/lignecommande/{id}', name: 'get_lignecommande', methods: ['GET'])]
    public function getLigneCommande(int $id, LigneCommandeRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $ligneCommande = $repository->find($id);
        if (!$ligneCommande) {
            return $this->json(['message' => 'Ligne de commande non trouvée'], 404);
        }
        
        $json = $serializer->serialize($ligneCommande, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/lignecommande/{id}', name: 'update_lignecommande', methods: ['PUT'])]
    public function updateLigneCommande(int $id, Request $request, EntityManagerInterface $em, LigneCommandeRepository $repository): JsonResponse
    {
        $ligneCommande = $repository->find($id);
        if (!$ligneCommande) {
            return $this->json(['message' => 'Ligne de commande non trouvée'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $ligneCommande->setProduit($data['produit'] ?? $ligneCommande->getProduit())
                      ->setQuantite($data['quantite'] ?? $ligneCommande->getQuantite())
                      ->setPrixUnitaire($data['prix_unitaire'] ?? $ligneCommande->getPrixUnitaire());

        if (isset($data['commande_id'])) {
            $commande = $em->getRepository(Commande::class)->find($data['commande_id']);
            if ($commande) {
                $ligneCommande->setCommande($commande);
            } else {
                return $this->json(['error' => 'Commande non trouvée'], 404);
            }
        }

        $em->flush();

        return $this->json($ligneCommande);
    }

    #[Route('/lignecommande/{id}', name: 'delete_lignecommande', methods: ['DELETE'])]
    public function deleteLigneCommande(int $id, EntityManagerInterface $em, LigneCommandeRepository $repository): JsonResponse
    {
        $ligneCommande = $repository->find($id);
        if (!$ligneCommande) {
            return $this->json(['message' => 'Ligne de commande non trouvée'], 404);
        }

        $em->remove($ligneCommande);
        $em->flush();
        
        return $this->json(['message' => 'Ligne de commande supprimée']);
    }
}
