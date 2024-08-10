<?php

namespace App\Controller;

use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RoleController extends AbstractController
{
    #[Route('/roles', name: 'get_roles', methods: ['GET'])]
    public function getRoles(EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $roles = $em->getRepository(Role::class)->findAll();
        $json = $serializer->serialize($roles, 'json', ['groups' => 'default']);
        
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/roles/{id}', name: 'get_role', methods: ['GET'])]
    public function getRole(int $id, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $role = $em->getRepository(Role::class)->find($id);
        if (!$role) {
            return $this->json(['message' => 'Role not found'], 404);
        }
        
        $json = $serializer->serialize($role, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/roles', name: 'create_role', methods: ['POST'])]
    public function createRole(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom'])) {
            return $this->json(['error' => 'Role name is required'], 400);
        }

        $role = new Role();
        $role->setNom($data['nom']);

        $em->persist($role);
        $em->flush();
        
        return $this->json($role, 201);
    }

    #[Route('/roles/{id}', name: 'update_role', methods: ['PUT'])]
    public function updateRole(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $role = $em->getRepository(Role::class)->find($id);
        if (!$role) {
            return $this->json(['message' => 'Role not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $role->setNom($data['nom'] ?? $role->getNom());

        $em->flush();
        
        return $this->json($role);
    }

    #[Route('/roles/{id}', name: 'delete_role', methods: ['DELETE'])]
    public function deleteRole(int $id, EntityManagerInterface $em): JsonResponse
    {
        $role = $em->getRepository(Role::class)->find($id);
        if (!$role) {
            return $this->json(['message' => 'Role not found'], 404);
        }

        $em->remove($role);
        $em->flush();
        
        return $this->json(['message' => 'Role deleted']);
    }
}
