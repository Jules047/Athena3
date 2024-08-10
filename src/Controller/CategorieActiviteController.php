<?php

namespace App\Controller;

use App\Entity\CategorieActivite;
use App\Repository\CategorieActiviteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CategorieActiviteController extends AbstractController
{
    #[Route('/categorie', name: 'create_categorie_activite', methods: ['POST'])]
    public function createCategorieActivite(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom'])) {
            return $this->json(['error' => 'Le champ nom est requis'], 400);
        }

        $categorieActivite = new CategorieActivite();
        $categorieActivite->setNom($data['nom']);
        $categorieActivite->setDescription($data['description'] ?? null);

        $em->persist($categorieActivite);
        $em->flush();

        return $this->json($categorieActivite, 201);
    }

    #[Route('/categorie', name: 'get_categorie_activites', methods: ['GET'])]
    public function getCategorieActivites(CategorieActiviteRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $categorieActivites = $repository->findAll();
        $json = $serializer->serialize($categorieActivites, 'json', ['groups' => 'default']);
        
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/categorie/{id}', name: 'get_categorie_activite', methods: ['GET'])]
    public function getCategorieActivite(int $id, CategorieActiviteRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $categorieActivite = $repository->find($id);
        if (!$categorieActivite) {
            return $this->json(['message' => 'Catégorie d\'activité non trouvée'], 404);
        }
        
        $json = $serializer->serialize($categorieActivite, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/categorie/{id}', name: 'update_categorie_activite', methods: ['PUT'])]
    public function updateCategorieActivite(int $id, Request $request, EntityManagerInterface $em, CategorieActiviteRepository $repository): JsonResponse
    {
        $categorieActivite = $repository->find($id);
        if (!$categorieActivite) {
            return $this->json(['message' => 'Catégorie d\'activité non trouvée'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) {
            $categorieActivite->setNom($data['nom']);
        }
        if (isset($data['description'])) {
            $categorieActivite->setDescription($data['description']);
        }

        $em->flush();
        
        return $this->json($categorieActivite);
    }

    #[Route('/categorie/{id}', name: 'delete_categorie_activite', methods: ['DELETE'])]
    public function deleteCategorieActivite(int $id, EntityManagerInterface $em, CategorieActiviteRepository $repository): JsonResponse
    {
        $categorieActivite = $repository->find($id);
        if (!$categorieActivite) {
            return $this->json(['message' => 'Catégorie d\'activité non trouvée'], 404);
        }

        $em->remove($categorieActivite);
        $em->flush();
        
        return $this->json(['message' => 'Catégorie d\'activité supprimée']);
    }
}
