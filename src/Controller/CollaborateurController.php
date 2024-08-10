<?php

namespace App\Controller;

use App\Entity\Collaborateur;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CollaborateurController extends AbstractController
{
    #[Route('/collaborateurs', name: 'get_collaborateurs', methods: ['GET'])]
    public function getCollaborateurs(EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $collaborateurs = $em->getRepository(Collaborateur::class)->findAll();
        $json = $serializer->serialize($collaborateurs, 'json', ['groups' => 'default']);
        
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/collaborateurs/{id}', name: 'get_collaborateur', methods: ['GET'])]
    public function getCollaborateur(int $id, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $collaborateur = $em->getRepository(Collaborateur::class)->find($id);
        if (!$collaborateur) {
            return $this->json(['message' => 'Collaborateur non trouvé'], 404);
        }
        
        $json = $serializer->serialize($collaborateur, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/collaborateurs', name: 'create_collaborateur', methods: ['POST'])]
    public function createCollaborateur(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['id'], $data['qualification'], $data['cout_horaire'], $data['coef_qualification'])) {
            return $this->json(['error' => 'Tous les champs sont requis'], 400);
        }

        $utilisateur = $em->getRepository(Utilisateur::class)->find($data['id']);
        if (!$utilisateur) {
            return $this->json(['error' => 'Utilisateur non trouvé'], 400);
        }

        $collaborateur = new Collaborateur();
        $collaborateur->setId($utilisateur)
                      ->setQualification($data['qualification'])
                      ->setCoutHoraire($data['cout_horaire'])
                      ->setCoefQualification($data['coef_qualification']);

        $em->persist($collaborateur);
        $em->flush();
        
        return $this->json($collaborateur, 201);
    }

    #[Route('/collaborateurs/{id}', name: 'update_collaborateur', methods: ['PUT'])]
    public function updateCollaborateur(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $collaborateur = $em->getRepository(Collaborateur::class)->find($id);
        if (!$collaborateur) {
            return $this->json(['message' => 'Collaborateur non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $collaborateur->setQualification($data['qualification'] ?? $collaborateur->getQualification())
                      ->setCoutHoraire($data['cout_horaire'] ?? $collaborateur->getCoutHoraire())
                      ->setCoefQualification($data['coef_qualification'] ?? $collaborateur->getCoefQualification());

        $em->flush();
        
        return $this->json($collaborateur);
    }

    #[Route('/collaborateurs/{id}', name: 'delete_collaborateur', methods: ['DELETE'])]
    public function deleteCollaborateur(int $id, EntityManagerInterface $em): JsonResponse
    {
        $collaborateur = $em->getRepository(Collaborateur::class)->find($id);
        if (!$collaborateur) {
            return $this->json(['message' => 'Collaborateur non trouvé'], 404);
        }

        $em->remove($collaborateur);
        $em->flush();
        
        return $this->json(['message' => 'Collaborateur supprimé']);
    }
}
