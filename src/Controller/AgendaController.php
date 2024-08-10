<?php

namespace App\Controller;

use App\Entity\Agenda;
use App\Entity\Utilisateur;
use App\Repository\AgendaRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AgendaController extends AbstractController
{
    #[Route('/agenda', name: 'create_agenda', methods: ['POST'])]
    public function createAgenda(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['utilisateur_id'])) {
            return $this->json(['error' => 'Le champ utilisateur_id est requis'], 400);
        }

        // Récupérer l'utilisateur
        $utilisateur = $em->getRepository(Utilisateur::class)->find($data['utilisateur_id']);
        if (!$utilisateur) {
            return $this->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        $agenda = new Agenda();
        $agenda->setUtilisateur($utilisateur);

        $em->persist($agenda);
        $em->flush();

        return $this->json($agenda, 201);
    }

    #[Route('/agenda', name: 'get_agendas', methods: ['GET'])]
    public function getAgendas(AgendaRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $agendas = $repository->findAll();
        $json = $serializer->serialize($agendas, 'json', ['groups' => 'default']);
        
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/agenda/{id}', name: 'get_agenda', methods: ['GET'])]
    public function getAgenda(int $id, AgendaRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $agenda = $repository->find($id);
        if (!$agenda) {
            return $this->json(['message' => 'Agenda non trouvé'], 404);
        }
        
        $json = $serializer->serialize($agenda, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/agenda/{id}', name: 'update_agenda', methods: ['PUT'])]
    public function updateAgenda(int $id, Request $request, EntityManagerInterface $em, AgendaRepository $repository): JsonResponse
    {
        $agenda = $repository->find($id);
        if (!$agenda) {
            return $this->json(['message' => 'Agenda non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['utilisateur_id'])) {
            $utilisateur = $em->getRepository(Utilisateur::class)->find($data['utilisateur_id']);
            if ($utilisateur) {
                $agenda->setUtilisateur($utilisateur);
            } else {
                return $this->json(['error' => 'Utilisateur non trouvé'], 404);
            }
        }

        $em->flush();

        return $this->json($agenda);
    }

    #[Route('/agenda/{id}', name: 'delete_agenda', methods: ['DELETE'])]
    public function deleteAgenda(int $id, EntityManagerInterface $em, AgendaRepository $repository): JsonResponse
    {
        $agenda = $repository->find($id);
        if (!$agenda) {
            return $this->json(['message' => 'Agenda non trouvé'], 404);
        }

        $em->remove($agenda);
        $em->flush();
        
        return $this->json(['message' => 'Agenda supprimé']);
    }
}
