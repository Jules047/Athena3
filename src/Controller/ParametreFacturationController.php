<?php

namespace App\Controller;

use App\Entity\ParametreFacturation;
use App\Entity\CategorieActivite;
use App\Repository\ParametreFacturationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ParametreFacturationController extends AbstractController
{
    #[Route('/parametre', name: 'create_parametrefacturation', methods: ['POST'])]
    public function createParametreFacturation(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['type_activite'], $data['taux_horaire'], $data['categorie_id'])) {
            return $this->json(['error' => 'Tous les champs sont requis'], 400);
        }

        $parametreFacturation = new ParametreFacturation();
        $parametreFacturation->setTypeActivite($data['type_activite']);
        $parametreFacturation->setTauxHoraire($data['taux_horaire']);

        // Récupérer la catégorie
        $categorie = $em->getRepository(CategorieActivite::class)->find($data['categorie_id']);
        if ($categorie) {
            $parametreFacturation->setCategorie($categorie);
        }

        $em->persist($parametreFacturation);
        $em->flush();

        return $this->json($parametreFacturation, 201);
    }

    #[Route('/parametre', name: 'get_parametrefacturations', methods: ['GET'])]
    public function getParametreFacturations(ParametreFacturationRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $parametresFacturation = $repository->findAll();
        $json = $serializer->serialize($parametresFacturation, 'json', ['groups' => 'default']);
        
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/parametre/{id}', name: 'get_parametrefacturation', methods: ['GET'])]
    public function getParametreFacturation(int $id, ParametreFacturationRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $parametreFacturation = $repository->find($id);
        if (!$parametreFacturation) {
            return $this->json(['message' => 'Paramètre de facturation non trouvé'], 404);
        }
        
        $json = $serializer->serialize($parametreFacturation, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/parametre/{id}', name: 'update_parametrefacturation', methods: ['PUT'])]
    public function updateParametreFacturation(int $id, Request $request, EntityManagerInterface $em, ParametreFacturationRepository $repository): JsonResponse
    {
        $parametreFacturation = $repository->find($id);
        if (!$parametreFacturation) {
            return $this->json(['message' => 'Paramètre de facturation non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $parametreFacturation->setTypeActivite($data['type_activite'] ?? $parametreFacturation->getTypeActivite())
                             ->setTauxHoraire($data['taux_horaire'] ?? $parametreFacturation->getTauxHoraire());

        if (isset($data['categorie_id'])) {
            $categorie = $em->getRepository(CategorieActivite::class)->find($data['categorie_id']);
            if ($categorie) {
                $parametreFacturation->setCategorie($categorie);
            }
        }

        $em->flush();

        return $this->json($parametreFacturation);
    }

    #[Route('/parametre/{id}', name: 'delete_parametrefacturation', methods: ['DELETE'])]
    public function deleteParametreFacturation(int $id, EntityManagerInterface $em, ParametreFacturationRepository $repository): JsonResponse
    {
        $parametreFacturation = $repository->find($id);
        if (!$parametreFacturation) {
            return $this->json(['message' => 'Paramètre de facturation non trouvé'], 404);
        }

        $em->remove($parametreFacturation);
        $em->flush();
        
        return $this->json(['message' => 'Paramètre de facturation supprimé']);
    }
}
