<?php

namespace App\Controller;

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\Menu;
use App\Entity\Fonction;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PermissionController extends AbstractController
{
    #[Route('/permissions', name: 'create_permission', methods: ['POST'])]
    public function createPermission(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['role_id'], $data['menu_id'], $data['fonction_id'])) {
            return $this->json(['error' => 'Tous les champs sont requis'], 400);
        }

        $role = $em->getRepository(Role::class)->find($data['role_id']);
        $menu = $em->getRepository(Menu::class)->find($data['menu_id']);
        $fonction = $em->getRepository(Fonction::class)->find($data['fonction_id']);

        if (!$role || !$menu || !$fonction) {
            return $this->json(['error' => 'Role, Menu ou Fonction non trouvé'], 404);
        }

        $permission = new Permission();
        $permission->setRole($role)
                   ->setMenu($menu)
                   ->setFonction($fonction);

        $em->persist($permission);
        $em->flush();

        return $this->json($permission, 201, [], ['groups' => 'default']);
    }

    #[Route('/permissions', name: 'get_permissions', methods: ['GET'])]
    public function getPermissions(PermissionRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $permissions = $repository->findAll();
        $json = $serializer->serialize($permissions, 'json', ['groups' => 'default']);
        
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/permissions/{id}', name: 'get_permission', methods: ['GET'])]
    public function getPermission(int $id, PermissionRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $permission = $repository->find($id);
        if (!$permission) {
            return $this->json(['message' => 'Permission non trouvée'], 404);
        }
        
        $json = $serializer->serialize($permission, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/permissions/{id}', name: 'update_permission', methods: ['PUT'])]
    public function updatePermission(int $id, Request $request, EntityManagerInterface $em, PermissionRepository $repository): JsonResponse
    {
        $permission = $repository->find($id);
        if (!$permission) {
            return $this->json(['message' => 'Permission non trouvée'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $role = $em->getRepository(Role::class)->find($data['role_id'] ?? $permission->getRole()->getId());
        $menu = $em->getRepository(Menu::class)->find($data['menu_id'] ?? $permission->getMenu()->getId());
        $fonction = $em->getRepository(Fonction::class)->find($data['fonction_id'] ?? $permission->getFonction()->getId());

        if ($role) $permission->setRole($role);
        if ($menu) $permission->setMenu($menu);
        if ($fonction) $permission->setFonction($fonction);

        $em->flush();
        
        return $this->json($permission, 200, [], ['groups' => 'default']);
    }

    #[Route('/permissions/{id}', name: 'delete_permission', methods: ['DELETE'])]
    public function deletePermission(int $id, EntityManagerInterface $em, PermissionRepository $repository): JsonResponse
    {
        $permission = $repository->find($id);
        if (!$permission) {
            return $this->json(['message' => 'Permission non trouvée'], 404);
        }

        $em->remove($permission);
        $em->flush();
        
        return $this->json(['message' => 'Permission supprimée']);
    }
}
