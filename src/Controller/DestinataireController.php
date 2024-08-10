<?php

namespace App\Controller;

use App\Entity\Destinataire;
use App\Repository\DestinataireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DestinataireController extends AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/destinataires', name: 'get_all_destinataires', methods: ['GET'])]
    public function getAllDestinataires(DestinataireRepository $repository): JsonResponse
    {
        $destinataires = $repository->findAll();

        if (empty($destinataires)) {
            return $this->json(['message' => 'Aucun destinataire trouvé'], 404);
        }

        $data = $this->serializer->normalize($destinataires, null, ['groups' => 'default']);
        return new JsonResponse($data);
    }

    #[Route('/destinataires/{id}', name: 'get_destinataire', methods: ['GET'])]
    public function getDestinataire(int $id, DestinataireRepository $repository): JsonResponse
    {
        $destinataire = $repository->find($id);

        if (!$destinataire) {
            return $this->json(['message' => 'Destinataire non trouvé'], 404);
        }

        $data = $this->serializer->normalize($destinataire, null, ['groups' => 'default']);
        return new JsonResponse($data);
    }

    #[Route('/destinataires', name: 'create_destinataire', methods: ['POST'])]
    public function createDestinataire(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['message_id'], $data['utilisateur_id'])) {
            return $this->json(['error' => 'Tous les champs sont requis'], 400);
        }

        $message = $em->getRepository(Message::class)->find($data['message_id']);
        $utilisateur = $em->getRepository(Utilisateur::class)->find($data['utilisateur_id']);

        if (!$message || !$utilisateur) {
            return $this->json(['error' => 'Message ou utilisateur non trouvé'], 404);
        }

        $destinataire = new Destinataire();
        $destinataire->setMessage($message)
                     ->setUtilisateur($utilisateur)
                     ->setLu($data['lu'] ?? false)
                     ->setDateLecture(isset($data['date_lecture']) ? new \DateTime($data['date_lecture']) : null);

        $em->persist($destinataire);
        $em->flush();

        $data = $this->serializer->normalize($destinataire, null, ['groups' => 'default']);
        return new JsonResponse($data, 201);
    }

    #[Route('/destinataires/{id}', name: 'update_destinataire', methods: ['PUT'])]
    public function updateDestinataire(int $id, Request $request, EntityManagerInterface $em, DestinataireRepository $repository): JsonResponse
    {
        $destinataire = $repository->find($id);

        if (!$destinataire) {
            return $this->json(['message' => 'Destinataire non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['lu'])) $destinataire->setLu($data['lu']);
        if (isset($data['date_lecture'])) $destinataire->setDateLecture(new \DateTime($data['date_lecture']));

        $em->flush();

        $data = $this->serializer->normalize($destinataire, null, ['groups' => 'default']);
        return new JsonResponse($data);
    }

    #[Route('/destinataires/{id}', name: 'delete_destinataire', methods: ['DELETE'])]
    public function deleteDestinataire(int $id, EntityManagerInterface $em, DestinataireRepository $repository): JsonResponse
    {
        $destinataire = $repository->find($id);

        if (!$destinataire) {
            return $this->json(['message' => 'Destinataire non trouvé'], 404);
        }

        $em->remove($destinataire);
        $em->flush();

        return $this->json(['message' => 'Destinataire supprimé']);
    }
}
