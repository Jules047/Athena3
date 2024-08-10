<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\OrdreDefabrication;
use App\Repository\DocumentRepository;
use App\Repository\OrdreDefabricationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DocumentController extends AbstractController
{
    #[Route('/document', name: 'create_document', methods: ['POST'])]
    public function createDocument(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom'], $data['type'], $data['contenu'], $data['of_id'])) {
            return $this->json(['error' => 'Tous les champs sont requis'], 400);
        }

        try {
            $document = new Document();
            $document->setNom($data['nom']);
            $document->setType($data['type']);
            $document->setContenu($data['contenu']); // Contenu comme chaîne de caractères

            $of = $em->getRepository(OrdreDefabrication::class)->find($data['of_id']);
            if (!$of) {
                return $this->json(['error' => 'Ordre de fabrication non trouvé'], 404);
            }

            $document->setOf($of);
            $document->setDateUpload(new \DateTime()); // Assurez-vous de définir la date d'upload

            $em->persist($document);
            $em->flush();

            return $this->json($document, 201);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Erreur lors de la création du document', 'exception' => $e->getMessage()], 500);
        }
    }

    #[Route('/document', name: 'get_documents', methods: ['GET'])]
    public function getDocuments(DocumentRepository $repository): JsonResponse
    {
        $documents = $repository->findAll();
        $data = [];
        
        foreach ($documents as $document) {
            $data[] = [
                'id' => $document->getId(),
                'nom' => $document->getNom(),
                'type' => $document->getType(),
                'contenu' => $document->getContenu(),
                'of_id' => $document->getOf()->getId()
            ];
        }
        
        return $this->json($data, 200);
    }

    #[Route('/document/{id}', name: 'get_document', methods: ['GET'])]
    public function getDocument(int $id, DocumentRepository $repository): JsonResponse
    {
        $document = $repository->find($id);
        if (!$document) {
            return $this->json(['message' => 'Document non trouvé'], 404);
        }

        $data = [
            'id' => $document->getId(),
            'nom' => $document->getNom(),
            'type' => $document->getType(),
            'contenu' => $document->getContenu(),
            'of_id' => $document->getOf()->getId()
        ];

        return $this->json($data, 200);
    }

    #[Route('/document/{id}', name: 'update_document', methods: ['PUT'])]
    public function updateDocument(int $id, Request $request, EntityManagerInterface $em, DocumentRepository $repository): JsonResponse
    {
        $document = $repository->find($id);
        if (!$document) {
            return $this->json(['message' => 'Document non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) {
            $document->setNom($data['nom']);
        }

        if (isset($data['type'])) {
            $document->setType($data['type']);
        }

        if (isset($data['contenu'])) {
            $document->setContenu($data['contenu']); // Contenu comme chaîne de caractères
        }

        if (isset($data['of_id'])) {
            $of = $em->getRepository(OrdreDefabrication::class)->find($data['of_id']);
            if ($of) {
                $document->setOf($of);
            } else {
                return $this->json(['error' => 'Ordre de fabrication non trouvé'], 404);
            }
        }

        $em->flush();

        return $this->json($document);
    }

    #[Route('/document/{id}', name: 'delete_document', methods: ['DELETE'])]
    public function deleteDocument(int $id, EntityManagerInterface $em, DocumentRepository $repository): JsonResponse
    {
        $document = $repository->find($id);
        if (!$document) {
            return $this->json(['message' => 'Document non trouvé'], 404);
        }

        $em->remove($document);
        $em->flush();
        
        return $this->json(['message' => 'Document supprimé']);
    }
}
