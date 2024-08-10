<?php

namespace App\Controller;

use App\Entity\RapportJournalier;
use App\Entity\Collaborateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RapportJournalierController extends AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/rapports', name: 'create_rapport_journalier', methods: ['POST'])]
    public function createRapportJournalier(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérification de la présence des champs requis
        if (!isset($data['date']) || !isset($data['collaborateur_id'])) {
            return $this->json(['error' => 'Tous les champs sont requis'], 400);
        }

        // Vérification de la validité du champ 'date'
        if (!\DateTime::createFromFormat('Y-m-d', $data['date'])) {
            return $this->json(['error' => 'Le format de la date est incorrect'], 400);
        }

        // Vérification que 'collaborateur_id' est un entier
        if (!is_int($data['collaborateur_id'])) {
            return $this->json(['error' => 'L\'ID du collaborateur doit être un entier'], 400);
        }

        $collaborateur = $em->getRepository(Collaborateur::class)->find($data['collaborateur_id']);
        if (!$collaborateur) {
            return $this->json(['error' => 'Collaborateur non trouvé'], 404);
        }

        $rapport = new RapportJournalier();
        $rapport->setDate(new \DateTime($data['date']));
        $rapport->setCollaborateur($collaborateur);

        $em->persist($rapport);
        $em->flush();

        return new JsonResponse($this->serializer->normalize($rapport, null, ['groups' => 'default']), 201);
    }

    #[Route('/rapports', name: 'get_all_rapports_journalier', methods: ['GET'])]
    public function getAllRapportsJournalier(EntityManagerInterface $em): JsonResponse
    {
        $rapports = $em->getRepository(RapportJournalier::class)->findAll();
        
        // Retourne un tableau de rapports
        return new JsonResponse($this->serializer->normalize($rapports, null, ['groups' => 'default']));
    }

    #[Route('/rapports/{id}', name: 'get_rapport_journalier', methods: ['GET'])]
    public function getRapportJournalier(int $id, EntityManagerInterface $em): JsonResponse
    {
        $rapport = $em->getRepository(RapportJournalier::class)->find($id);
        if (!$rapport) {
            return $this->json(['message' => 'Rapport journalier non trouvé'], 404);
        }

        return new JsonResponse($this->serializer->normalize($rapport, null, ['groups' => 'default']));
    }

    #[Route('/rapports/{id}', name: 'update_rapport_journalier', methods: ['PUT'])]
    public function updateRapportJournalier(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $rapport = $em->getRepository(RapportJournalier::class)->find($id);
        if (!$rapport) {
            return $this->json(['message' => 'Rapport journalier non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['date'])) {
            if (!\DateTime::createFromFormat('Y-m-d', $data['date'])) {
                return $this->json(['error' => 'Le format de la date est incorrect'], 400);
            }
            $rapport->setDate(new \DateTime($data['date']));
        }

        if (isset($data['collaborateur_id'])) {
            if (!is_int($data['collaborateur_id'])) {
                return $this->json(['error' => 'L\'ID du collaborateur doit être un entier'], 400);
            }
            $collaborateur = $em->getRepository(Collaborateur::class)->find($data['collaborateur_id']);
            if ($collaborateur) {
                $rapport->setCollaborateur($collaborateur);
            } else {
                return $this->json(['error' => 'Collaborateur non trouvé'], 404);
            }
        }

        $em->flush();

        return new JsonResponse($this->serializer->normalize($rapport, null, ['groups' => 'default']));
    }

    #[Route('/rapports/{id}', name: 'delete_rapport_journalier', methods: ['DELETE'])]
    public function deleteRapportJournalier(int $id, EntityManagerInterface $em): JsonResponse
    {
        $rapport = $em->getRepository(RapportJournalier::class)->find($id);
        if (!$rapport) {
            return $this->json(['message' => 'Rapport journalier non trouvé'], 404);
        }

        $em->remove($rapport);
        $em->flush();

        return $this->json(['message' => 'Rapport journalier supprimé']);
    }
}
