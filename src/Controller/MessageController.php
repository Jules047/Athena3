<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Utilisateur;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class MessageController extends AbstractController
{
    #[Route('/messages', name: 'create_message', methods: ['POST'])]
    public function createMessage(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['objet'], $data['contenu'], $data['expediteur_id'])) {
            return $this->json(['error' => 'Tous les champs sont requis'], 400);
        }

        $expediteur = $em->getRepository(Utilisateur::class)->find($data['expediteur_id']);
        if (!$expediteur) {
            return $this->json(['error' => 'Expéditeur non trouvé'], 404);
        }

        $message = new Message();
        $message->setObjet($data['objet'])
                ->setContenu($data['contenu'])
                ->setExpediteur($expediteur)
                ->setDateEnvoi(new \DateTime())
                ->setArchive($data['archive'] ?? false);

        $em->persist($message);
        $em->flush();

        return $this->json($message, 201);
    }

    #[Route('/messages', name: 'get_messages', methods: ['GET'])]
    public function getMessages(MessageRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $messages = $repository->findAll();
        $json = $serializer->serialize($messages, 'json', ['groups' => 'default']);
        
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/messages/{id}', name: 'get_message', methods: ['GET'])]
    public function getMessage(int $id, MessageRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $message = $repository->find($id);
        if (!$message) {
            return $this->json(['message' => 'Message non trouvé'], 404);
        }
        
        $json = $serializer->serialize($message, 'json', ['groups' => 'default']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/messages/{id}', name: 'update_message', methods: ['PUT'])]
    public function updateMessage(int $id, Request $request, EntityManagerInterface $em, MessageRepository $repository): JsonResponse
    {
        $message = $repository->find($id);
        if (!$message) {
            return $this->json(['message' => 'Message non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $expediteur = $em->getRepository(Utilisateur::class)->find($data['expediteur_id'] ?? $message->getExpediteur()->getId());
        if ($expediteur) $message->setExpediteur($expediteur);
        
        $message->setObjet($data['objet'] ?? $message->getObjet())
                ->setContenu($data['contenu'] ?? $message->getContenu())
                ->setArchive($data['archive'] ?? $message->isArchive());

        $em->flush();
        
        return $this->json($message);
    }

    #[Route('/messages/{id}', name: 'delete_message', methods: ['DELETE'])]
    public function deleteMessage(int $id, EntityManagerInterface $em, MessageRepository $repository): JsonResponse
    {
        $message = $repository->find($id);
        if (!$message) {
            return $this->json(['message' => 'Message non trouvé'], 404);
        }

        $em->remove($message);
        $em->flush();
        
        return $this->json(['message' => 'Message supprimé']);
    }
}
