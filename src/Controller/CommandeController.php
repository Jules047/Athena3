<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Collaborateur;
use App\Entity\OrdreDefabrication;
use App\Repository\CommandeRepository;
use App\Repository\CollaborateurRepository;
use App\Repository\OrdreDefabricationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'create_commande', methods: ['POST'])]
    public function createCommande(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['montant_total'], $data['statut'], $data['collaborateur_id'])) {
            return $this->json(['error' => 'Tous les champs requis ne sont pas présents'], 400);
        }

        $collaborateur = $em->getRepository(Collaborateur::class)->find($data['collaborateur_id']);
        if (!$collaborateur) {
            return $this->json(['error' => 'Collaborateur non trouvé'], 404);
        }

        $commande = new Commande();
        $commande->setMontantTotal($data['montant_total']);
        $commande->setStatut($data['statut']);
        $commande->setCollaborateur($collaborateur);

        if (isset($data['of_id'])) {
            $ordreDefabrication = $em->getRepository(OrdreDefabrication::class)->find($data['of_id']);
            if ($ordreDefabrication) {
                $commande->setOrdreDefabrication($ordreDefabrication);
            }
        }

        $em->persist($commande);
        $em->flush();

        return $this->json($commande, 201);
    }

    #[Route('/commande', name: 'get_commandes', methods: ['GET'])]
    public function getCommandes(CommandeRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $commandes = $repository->findAll();
        $json = $serializer->serialize($commandes, 'json', ['groups' => 'default']);
        
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/commande/{id}', name: 'get_commande', methods: ['GET'])]
    public function getCommande(int $id, CommandeRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $commande = $repository->find($id);
        if (!$commande) {
            return $this->json(['message' => 'Commande non trouvée'], 404);
        }
        
        $json = $serializer->serialize($commande, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/commande/{id}', name: 'update_commande', methods: ['PUT'])]
    public function updateCommande(int $id, Request $request, EntityManagerInterface $em, CommandeRepository $repository): JsonResponse
    {
        $commande = $repository->find($id);
        if (!$commande) {
            return $this->json(['message' => 'Commande non trouvée'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['montant_total'])) {
            $commande->setMontantTotal($data['montant_total']);
        }
        if (isset($data['statut'])) {
            $commande->setStatut($data['statut']);
        }
        if (isset($data['collaborateur_id'])) {
            $collaborateur = $em->getRepository(Collaborateur::class)->find($data['collaborateur_id']);
            if ($collaborateur) {
                $commande->setCollaborateur($collaborateur);
            } else {
                return $this->json(['error' => 'Collaborateur non trouvé'], 404);
            }
        }
        if (isset($data['of_id'])) {
            $ordreDefabrication = $em->getRepository(OrdreDefabrication::class)->find($data['of_id']);
            if ($ordreDefabrication) {
                $commande->setOrdreDefabrication($ordreDefabrication);
            }
        }

        $em->flush();

        return $this->json($commande);
    }

    #[Route('/commande/{id}', name: 'delete_commande', methods: ['DELETE'])]
    public function deleteCommande(int $id, EntityManagerInterface $em, CommandeRepository $repository): JsonResponse
    {
        $commande = $repository->find($id);
        if (!$commande) {
            return $this->json(['message' => 'Commande non trouvée'], 404);
        }

        $em->remove($commande);
        $em->flush();
        
        return $this->json(['message' => 'Commande supprimée']);
    }
}
