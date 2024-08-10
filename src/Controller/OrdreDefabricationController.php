<?php

namespace App\Controller;

use App\Entity\OrdreDefabrication;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class OrdreDefabricationController extends AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/ordres', name: 'get_all_ordres_defabrication', methods: ['GET'])]
    public function getAllordres(EntityManagerInterface $em): JsonResponse
    {
        $ordres = $em->getRepository(OrdreDefabrication::class)->findAll();

        return new JsonResponse($this->serializer->normalize($ordres, null, ['groups' => 'default']));
    }

    #[Route('/ordres', name: 'create_ordre_defabrication', methods: ['POST'])]
    public function createOrdreDefabrication(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom'], $data['client'], $data['duree_estimee'], $data['createur_id'])) {
            return $this->json(['error' => 'Tous les champs sont requis'], 400);
        }

        $createur = $em->getRepository(Utilisateur::class)->find($data['createur_id']);
        if (!$createur) {
            return $this->json(['error' => 'Créateur non trouvé'], 404);
        }

        $ordre = new OrdreDefabrication();
        $ordre->setNom($data['nom'])
              ->setClient($data['client'])
              ->setDescription($data['description'] ?? null)
              ->setDureeEstimee($data['duree_estimee'])
              ->setCreateur($createur)
              ->setDateCreation(new \DateTime())
              ->setDateModification(new \DateTime())
              ->setStatut('En attente');

        $em->persist($ordre);
        $em->flush();

        return new JsonResponse($this->serializer->normalize($ordre, null, ['groups' => 'default']), 201);
    }

    #[Route('/ordres/{id}', name: 'get_ordre_defabrication', methods: ['GET'])]
    public function getOrdreDefabrication(int $id, EntityManagerInterface $em): JsonResponse
    {
        $ordre = $em->getRepository(OrdreDefabrication::class)->find($id);
        if (!$ordre) {
            return $this->json(['message' => 'Ordre de fabrication non trouvé'], 404);
        }

        return new JsonResponse($this->serializer->normalize($ordre, null, ['groups' => 'default']));
    }

    #[Route('/ordres/{id}', name: 'update_ordre_defabrication', methods: ['PUT'])]
    public function updateOrdreDefabrication(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $ordre = $em->getRepository(OrdreDefabrication::class)->find($id);
        if (!$ordre) {
            return $this->json(['message' => 'Ordre de fabrication non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) $ordre->setNom($data['nom']);
        if (isset($data['client'])) $ordre->setClient($data['client']);
        if (isset($data['description'])) $ordre->setDescription($data['description']);
        if (isset($data['duree_estimee'])) $ordre->setDureeEstimee($data['duree_estimee']);
        if (isset($data['createur_id'])) {
            $createur = $em->getRepository(Utilisateur::class)->find($data['createur_id']);
            if ($createur) $ordre->setCreateur($createur);
        }

        $em->flush();

        return new JsonResponse($this->serializer->normalize($ordre, null, ['groups' => 'default']));
    }

    #[Route('/ordres/{id}', name: 'delete_ordre_defabrication', methods: ['DELETE'])]
    public function deleteOrdreDefabrication(int $id, EntityManagerInterface $em): JsonResponse
    {
        $ordre = $em->getRepository(OrdreDefabrication::class)->find($id);
        if (!$ordre) {
            return $this->json(['message' => 'Ordre de fabrication non trouvé'], 404);
        }

        $em->remove($ordre);
        $em->flush();

        return $this->json(['message' => 'Ordre de fabrication supprimé']);
    }
}
