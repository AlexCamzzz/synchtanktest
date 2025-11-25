<?php

namespace App\Controller;

use App\Service\TrackService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/tracks')]
class TrackController extends AbstractController
{
    public function __construct(
        private readonly TrackService $trackService
    ) {
    }

    #[Route('', name: 'get_tracks', methods: ['GET'])]
    public function getTracks(): JsonResponse
    {
        $tracks = $this->trackService->getAllTracks();
        
        return $this->json($tracks);
    }

    #[Route('', name: 'create_track', methods: ['POST'])]
    public function createTrack(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $result = $this->trackService->createTrack($data);

        if (!$result['success']) {
            return $this->json(['errors' => $result['errors']], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($result['track'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update_track', methods: ['PUT'])]
    public function updateTrack(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $result = $this->trackService->updateTrack($id, $data);

        if (!$result['success']) {
            if (in_array('Track not found', $result['errors'])) {
                return $this->json(['error' => 'Track not found'], Response::HTTP_NOT_FOUND);
            }

            return $this->json(['errors' => $result['errors']], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($result['track']);
    }
}
