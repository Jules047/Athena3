<?php

namespace App\Controller;

use App\Entity\Fonction;
use App\Repository\FonctionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class FonctionController extends AbstractController
{
    #[Route('/fonctions', name: 'get_fonctions', methods: ['GET'])]
    public function getFonctions(FonctionRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $fonctions = $repository->findAll();
        $json = $serializer->serialize($fonctions, 'json', ['groups' => 'default']);
        
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/fonctions/{id}', name: 'get_fonction', methods: ['GET'])]
    public function getFonction(int $id, FonctionRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $fonction = $repository->find($id);
        if (!$fonction) {
            return $this->json(['message' => 'Fonction non trouvée'], 404);
        }
        
        $json = $serializer->serialize($fonction, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/fonctions', name: 'create_fonction', methods: ['POST'])]
    public function createFonction(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom'])) {
            return $this->json(['error' => 'Le champ nom est requis'], 400);
        }

        $fonction = new Fonction();
        $fonction->setNom($data['nom']);

        $em->persist($fonction);
        $em->flush();
        
        return $this->json($fonction, 201);
    }

    #[Route('/fonctions/{id}', name: 'update_fonction', methods: ['PUT'])]
    public function updateFonction(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $fonction = $em->getRepository(Fonction::class)->find($id);
        if (!$fonction) {
            return $this->json(['message' => 'Fonction non trouvée'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $fonction->setNom($data['nom'] ?? $fonction->getNom());

        $em->flush();
        
        return $this->json($fonction);
    }

    #[Route('/fonctions/{id}', name: 'delete_fonction', methods: ['DELETE'])]
    public function deleteFonction(int $id, EntityManagerInterface $em): JsonResponse
    {
        $fonction = $em->getRepository(Fonction::class)->find($id);
        if (!$fonction) {
            return $this->json(['message' => 'Fonction non trouvée'], 404);
        }

        $em->remove($fonction);
        $em->flush();
        
        return $this->json(['message' => 'Fonction supprimée']);
    }
}
