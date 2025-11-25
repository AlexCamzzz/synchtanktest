<?php

namespace App\Service;

use App\Entity\Track;
use App\Repository\TrackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TrackService
{
    public function __construct(
        private readonly TrackRepository $trackRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator
    ) {
    }

    /**
     * Get all tracks
     *
     * @return Track[]
     */
    public function getAllTracks(): array
    {
        return $this->trackRepository->findAll();
    }

    /**
     * Get a track by ID
     *
     * @param int $id
     * @return Track|null
     */
    public function getTrackById(int $id): ?Track
    {
        return $this->trackRepository->find($id);
    }

    /**
     * Create a new track
     *
     * @param array $data
     * @return array
     */
    public function createTrack(array $data): array
    {
        $track = new Track();
        $this->setTrackData($track, $data);

        $errors = $this->validator->validate($track);
        if (count($errors) > 0) {
            return [
                'success' => false,
                'errors' => $this->formatValidationErrors($errors)
            ];
        }

        $this->entityManager->persist($track);
        $this->entityManager->flush();

        return [
            'success' => true,
            'track' => $track
        ];
    }

    /**
     * Update an existing track
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateTrack(int $id, array $data): array
    {
        $track = $this->trackRepository->find($id);
        if (!$track) {
            return [
                'success' => false,
                'errors' => ['Track not found']
            ];
        }

        $this->setTrackData($track, $data);

        $errors = $this->validator->validate($track);
        if (count($errors) > 0) {
            return [
                'success' => false,
                'errors' => $this->formatValidationErrors($errors)
            ];
        }

        $this->entityManager->flush();

        return [
            'success' => true,
            'track' => $track
        ];
    }

    /**
     * Set track data from array
     *
     * @param Track $track
     * @param array $data
     */
    private function setTrackData(Track $track, array $data): void
    {
        if (isset($data['title'])) {
            $track->setTitle($data['title']);
        }

        if (isset($data['artist'])) {
            $track->setArtist($data['artist']);
        }

        if (isset($data['duration'])) {
            $track->setDuration($data['duration']);
        }

        if (array_key_exists('isrc', $data)) {
            $track->setIsrc($data['isrc']);
        }
    }

    /**
     * Format validation errors
     *
     * @param ConstraintViolationListInterface $errors
     * @return array
     */
    private function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $formattedErrors = [];
        foreach ($errors as $error) {
            $formattedErrors[$error->getPropertyPath()] = $error->getMessage();
        }
        return $formattedErrors;
    }
}
