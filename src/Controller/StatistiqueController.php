<?php

namespace App\Controller;

use App\Entity\Statistique;
use App\Repository\StatistiqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class StatistiqueController extends AbstractController
{
    #[Route('/statistique', name: 'create_statistique', methods: ['POST'])]
    public function createStatistique(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['type'], $data['periode'], $data['donnees'])) {
            return $this->json(['error' => 'Tous les champs sont requis'], 400);
        }

        $statistique = new Statistique();
        $statistique->setType($data['type']);
        $statistique->setPeriode($data['periode']);
        $statistique->setDonnees($data['donnees']);
        $statistique->setDateGeneration(new \DateTime());

        $em->persist($statistique);
        $em->flush();

        return $this->json($statistique, 201);
    }

    #[Route('/statistique', name: 'get_statistiques', methods: ['GET'])]
    public function getStatistiques(StatistiqueRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $statistiques = $repository->findAll();
        $json = $serializer->serialize($statistiques, 'json', ['groups' => 'default']);
        
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/statistique/{id}', name: 'get_statistique', methods: ['GET'])]
    public function getStatistique(int $id, StatistiqueRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $statistique = $repository->find($id);
        if (!$statistique) {
            return $this->json(['message' => 'Statistique non trouvée'], 404);
        }
        
        $json = $serializer->serialize($statistique, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/statistique/{id}', name: 'update_statistique', methods: ['PUT'])]
    public function updateStatistique(int $id, Request $request, EntityManagerInterface $em, StatistiqueRepository $repository): JsonResponse
    {
        $statistique = $repository->find($id);
        if (!$statistique) {
            return $this->json(['message' => 'Statistique non trouvée'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['type'])) {
            $statistique->setType($data['type']);
        }
        if (isset($data['periode'])) {
            $statistique->setPeriode($data['periode']);
        }
        if (isset($data['donnees'])) {
            $statistique->setDonnees($data['donnees']);
        }

        $em->flush();

        return $this->json($statistique);
    }

    #[Route('/statistique/{id}', name: 'delete_statistique', methods: ['DELETE'])]
    public function deleteStatistique(int $id, EntityManagerInterface $em, StatistiqueRepository $repository): JsonResponse
    {
        $statistique = $repository->find($id);
        if (!$statistique) {
            return $this->json(['message' => 'Statistique non trouvée'], 404);
        }

        $em->remove($statistique);
        $em->flush();
        
        return $this->json(['message' => 'Statistique supprimée']);
    }
}
