<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UtilisateurController extends AbstractController
{
    #[Route('/utilisateurs', name: 'get_utilisateurs', methods: ['GET'])]
    public function getUtilisateurs(EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $utilisateurs = $em->getRepository(Utilisateur::class)->findAll();
        $json = $serializer->serialize($utilisateurs, 'json', ['groups' => 'default']);
        
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/utilisateurs/{id}', name: 'get_utilisateur', methods: ['GET'])]
    public function getUtilisateur(int $id, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $utilisateur = $em->getRepository(Utilisateur::class)->find($id);
        if (!$utilisateur) {
            return $this->json(['message' => 'Utilisateur non trouvé'], 404);
        }
        
        $json = $serializer->serialize($utilisateur, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/utilisateurs', name: 'create_utilisateur', methods: ['POST'])]
    public function createUtilisateur(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom'], $data['prenom'], $data['email'], $data['mot_de_passe'], $data['role_id'])) {
            return $this->json(['error' => 'Tous les champs sont requis'], 400);
        }

        $role = $em->getRepository(Role::class)->find($data['role_id']);
        if (!$role) {
            return $this->json(['error' => 'Rôle non trouvé'], 400);
        }

        $utilisateur = new Utilisateur();
        $utilisateur->setNom($data['nom'])
                    ->setPrenom($data['prenom'])
                    ->setEmail($data['email'])
                    ->setMotDePasse($passwordHasher->hashPassword($utilisateur, $data['mot_de_passe']))
                    ->setRole($role);

        $em->persist($utilisateur);
        $em->flush();
        
        return $this->json($utilisateur, 201);
    }

    #[Route('/utilisateurs/{id}', name: 'update_utilisateur', methods: ['PUT'])]
    public function updateUtilisateur(int $id, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $utilisateur = $em->getRepository(Utilisateur::class)->find($id);
        if (!$utilisateur) {
            return $this->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $utilisateur->setNom($data['nom'] ?? $utilisateur->getNom())
                    ->setPrenom($data['prenom'] ?? $utilisateur->getPrenom())
                    ->setEmail($data['email'] ?? $utilisateur->getEmail());

        if (isset($data['mot_de_passe'])) {
            $utilisateur->setMotDePasse($passwordHasher->hashPassword($utilisateur, $data['mot_de_passe']));
        }

        if (isset($data['role_id'])) {
            $role = $em->getRepository(Role::class)->find($data['role_id']);
            if ($role) {
                $utilisateur->setRole($role);
            }
        }

        $utilisateur->updateDateModification();

        $em->flush();
        
        return $this->json($utilisateur);
    }

    #[Route('/utilisateurs/{id}', name: 'delete_utilisateur', methods: ['DELETE'])]
    public function deleteUtilisateur(int $id, EntityManagerInterface $em): JsonResponse
    {
        $utilisateur = $em->getRepository(Utilisateur::class)->find($id);
        if (!$utilisateur) {
            return $this->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $em->remove($utilisateur);
        $em->flush();
        
        return $this->json(['message' => 'Utilisateur supprimé']);
    }
}
