<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class MenuController extends AbstractController
{
    #[Route('/menus', name: 'get_menus', methods: ['GET'])]
    public function getMenus(MenuRepository $menuRepository, SerializerInterface $serializer): JsonResponse
    {
        $menus = $menuRepository->findAll();
        $json = $serializer->serialize($menus, 'json', ['groups' => 'default']);
        
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/menus/{id}', name: 'get_menu', methods: ['GET'])]
    public function getMenu(int $id, MenuRepository $menuRepository, SerializerInterface $serializer): JsonResponse
    {
        $menu = $menuRepository->find($id);
        if (!$menu) {
            return $this->json(['message' => 'Menu non trouvé'], 404);
        }

        $json = $serializer->serialize($menu, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/menus', name: 'create_menu', methods: ['POST'])]
    public function createMenu(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom'], $data['code'])) {
            return $this->json(['error' => 'Les champs nom et code sont requis'], 400);
        }

        $menu = new Menu();
        $menu->setNom($data['nom'])
            ->setCode($data['code'])
            ->setDescription($data['description'] ?? null);

        if (isset($data['parent_id'])) {
            $parent = $em->getRepository(Menu::class)->find($data['parent_id']);
            if ($parent) {
                $menu->setParent($parent);
            } else {
                return $this->json(['error' => 'Menu parent non trouvé'], 404);
            }
        }

        $em->persist($menu);
        $em->flush();
        
        $json = $serializer->serialize($menu, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 201, [], true);
    }

    #[Route('/menus/{id}', name: 'update_menu', methods: ['PUT'])]
    public function updateMenu(int $id, Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $menu = $em->getRepository(Menu::class)->find($id);
        if (!$menu) {
            return $this->json(['message' => 'Menu non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $menu->setNom($data['nom'] ?? $menu->getNom())
            ->setCode($data['code'] ?? $menu->getCode())
            ->setDescription($data['description'] ?? $menu->getDescription());

        if (isset($data['parent_id'])) {
            $parent = $em->getRepository(Menu::class)->find($data['parent_id']);
            if ($parent) {
                $menu->setParent($parent);
            } else {
                return $this->json(['error' => 'Menu parent non trouvé'], 404);
            }
        }

        $em->flush();
        
        $json = $serializer->serialize($menu, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/menus/{id}', name: 'delete_menu', methods: ['DELETE'])]
    public function deleteMenu(int $id, EntityManagerInterface $em): JsonResponse
    {
        $menu = $em->getRepository(Menu::class)->find($id);
        if (!$menu) {
            return $this->json(['message' => 'Menu non trouvé'], 404);
        }

        $em->remove($menu);
        $em->flush();
        
        return $this->json(['message' => 'Menu supprimé']);
    }
}
