<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Agenda;
use App\Repository\EvenementRepository;
use App\Repository\AgendaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'create_evenement', methods: ['POST'])]
    public function createEvenement(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['agenda_id'], $data['titre'], $data['date_debut'], $data['date_fin'], $data['type'])) {
            return $this->json(['error' => 'Tous les champs sont requis'], 400);
        }

        $agenda = $em->getRepository(Agenda::class)->find($data['agenda_id']);
        if (!$agenda) {
            return $this->json(['error' => 'Agenda non trouvé'], 404);
        }

        $evenement = new Evenement();
        $evenement->setAgenda($agenda);
        $evenement->setTitre($data['titre']);
        $evenement->setDescription($data['description'] ?? null);
        $evenement->setDateDebut(new \DateTime($data['date_debut']));
        $evenement->setDateFin(new \DateTime($data['date_fin']));
        $evenement->setType($data['type']); // Utiliser directement la chaîne de caractères

        $em->persist($evenement);
        $em->flush();

        return $this->json($evenement, 201);
    }

    #[Route('/evenement', name: 'get_evenements', methods: ['GET'])]
    public function getEvenements(EvenementRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $evenements = $repository->findAll();
        $json = $serializer->serialize($evenements, 'json', ['groups' => 'default']);
        
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/evenement/{id}', name: 'get_evenement', methods: ['GET'])]
    public function getEvenement(int $id, EvenementRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $evenement = $repository->find($id);
        if (!$evenement) {
            return $this->json(['message' => 'Événement non trouvé'], 404);
        }
        
        $json = $serializer->serialize($evenement, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/evenement/{id}', name: 'update_evenement', methods: ['PUT'])]
    public function updateEvenement(int $id, Request $request, EntityManagerInterface $em, EvenementRepository $repository): JsonResponse
    {
        $evenement = $repository->find($id);
        if (!$evenement) {
            return $this->json(['message' => 'Événement non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['agenda_id'])) {
            $agenda = $em->getRepository(Agenda::class)->find($data['agenda_id']);
            if ($agenda) {
                $evenement->setAgenda($agenda);
            } else {
                return $this->json(['error' => 'Agenda non trouvé'], 404);
            }
        }

        $evenement->setTitre($data['titre'] ?? $evenement->getTitre());
        $evenement->setDescription($data['description'] ?? $evenement->getDescription());
        $evenement->setDateDebut(new \DateTime($data['date_debut'] ?? $evenement->getDateDebut()->format(\DateTime::ISO8601)));
        $evenement->setDateFin(new \DateTime($data['date_fin'] ?? $evenement->getDateFin()->format(\DateTime::ISO8601)));
        $evenement->setType($data['type'] ?? $evenement->getType());

        $em->flush();

        return $this->json($evenement);
    }

    #[Route('/evenement/{id}', name: 'delete_evenement', methods: ['DELETE'])]
    public function deleteEvenement(int $id, EntityManagerInterface $em, EvenementRepository $repository): JsonResponse
    {
        $evenement = $repository->find($id);
        if (!$evenement) {
            return $this->json(['message' => 'Événement non trouvé'], 404);
        }

        $em->remove($evenement);
        $em->flush();
        
        return $this->json(['message' => 'Événement supprimé']);
    }
}
