<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Entity\RapportJournalier;
use App\Entity\OrdreDefabrication;
use App\Repository\ActiviteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ActiviteController extends AbstractController
{
    #[Route('/activites', name: 'create_activite', methods: ['POST'])]
    public function createActivite(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['duree'], $data['type'], $data['rapport_id'])) {
            return $this->json(['error' => 'Tous les champs obligatoires ne sont pas remplis'], 400);
        }

        $rapport = $em->getRepository(RapportJournalier::class)->find($data['rapport_id']);
        if (!$rapport) {
            return $this->json(['error' => 'Rapport non trouvé'], 404);
        }

        $activite = new Activite();
        $activite->setDuree($data['duree'])
                 ->setType($data['type'])
                 ->setDescription($data['description'] ?? null)
                 ->setRapport($rapport);

        if (isset($data['of_id'])) {
            $ordreDeFabrication = $em->getRepository(OrdreDefabrication::class)->find($data['of_id']);
            $activite->setOrdreDeFabrication($ordreDeFabrication);
        }

        $em->persist($activite);
        $em->flush();

        return $this->json($activite, 201);
    }

    #[Route('/activites', name: 'get_all_activites', methods: ['GET'])]
    public function getAllActivites(ActiviteRepository $repository): JsonResponse
    {
        $activites = $repository->findAll();
        return $this->json($activites);
    }

    #[Route('/activites/{id}', name: 'get_activite', methods: ['GET'])]
    public function getActivite(int $id, ActiviteRepository $repository): JsonResponse
    {
        $activite = $repository->find($id);
        if (!$activite) {
            return $this->json(['message' => 'Activité non trouvée'], 404);
        }

        return $this->json($activite);
    }

    #[Route('/activites/{id}', name: 'update_activite', methods: ['PUT'])]
    public function updateActivite(int $id, Request $request, EntityManagerInterface $em, ActiviteRepository $repository): JsonResponse
    {
        $activite = $repository->find($id);
        if (!$activite) {
            return $this->json(['message' => 'Activité non trouvée'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['duree'])) {
            $activite->setDuree($data['duree']);
        }

        if (isset($data['type'])) {
            $activite->setType($data['type']);
        }

        if (isset($data['description'])) {
            $activite->setDescription($data['description']);
        }

        if (isset($data['rapport_id'])) {
            $rapport = $em->getRepository(RapportJournalier::class)->find($data['rapport_id']);
            if ($rapport) {
                $activite->setRapport($rapport);
            }
        }

        if (isset($data['of_id'])) {
            $ordreDeFabrication = $em->getRepository(OrdreDefabrication::class)->find($data['of_id']);
            if ($ordreDeFabrication) {
                $activite->setOrdreDeFabrication($ordreDeFabrication);
            }
        }

        $em->flush();

        return $this->json($activite);
    }

    #[Route('/activites/{id}', name: 'delete_activite', methods: ['DELETE'])]
    public function deleteActivite(int $id, EntityManagerInterface $em, ActiviteRepository $repository): JsonResponse
    {
        $activite = $repository->find($id);
        if (!$activite) {
            return $this->json(['message' => 'Activité non trouvée'], 404);
        }

        $em->remove($activite);
        $em->flush();

        return $this->json(['message' => 'Activité supprimée']);
    }
}
