<?php

namespace App\Tests\Service;

use App\Entity\Track;
use App\Repository\TrackRepository;
use App\Service\TrackService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TrackServiceTest extends TestCase
{
    private TrackRepository $trackRepository;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private TrackService $trackService;

    protected function setUp(): void
    {
        $this->trackRepository = $this->createMock(TrackRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        
        $this->trackService = new TrackService(
            $this->trackRepository,
            $this->entityManager,
            $this->validator
        );
    }

    public function testGetAllTracks(): void
    {
        // Create mock tracks
        $track1 = new Track();
        $track1->setTitle('Track 1');
        $track1->setArtist('Artist 1');
        $track1->setDuration(180);

        $track2 = new Track();
        $track2->setTitle('Track 2');
        $track2->setArtist('Artist 2');
        $track2->setDuration(240);

        $expectedTracks = [$track1, $track2];

        // Configure mock repository
        $this->trackRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($expectedTracks);

        // Call the service method
        $result = $this->trackService->getAllTracks();

        // Assert the result
        $this->assertSame($expectedTracks, $result);
    }

    public function testGetTrackById(): void
    {
        // Create mock track
        $track = new Track();
        $track->setTitle('Test Track');
        $track->setArtist('Test Artist');
        $track->setDuration(180);

        // Configure mock repository
        $this->trackRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($track);

        // Call the service method
        $result = $this->trackService->getTrackById(1);

        // Assert the result
        $this->assertSame($track, $result);
    }

    public function testCreateTrackSuccess(): void
    {
        // Test data
        $trackData = [
            'title' => 'New Track',
            'artist' => 'New Artist',
            'duration' => 200,
            'isrc' => 'US-ABC-12-12345'
        ];

        // Configure validator mock to return no errors
        $this->validator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        // Configure entity manager mock
        $this->entityManager->expects($this->once())
            ->method('persist');
        $this->entityManager->expects($this->once())
            ->method('flush');

        // Call the service method
        $result = $this->trackService->createTrack($trackData);

        // Assert the result
        $this->assertTrue($result['success']);
        $this->assertInstanceOf(Track::class, $result['track']);
        $this->assertEquals($trackData['title'], $result['track']->getTitle());
        $this->assertEquals($trackData['artist'], $result['track']->getArtist());
        $this->assertEquals($trackData['duration'], $result['track']->getDuration());
        $this->assertEquals($trackData['isrc'], $result['track']->getIsrc());
    }

    public function testCreateTrackValidationFailure(): void
    {
        // Test data with missing required fields
        $trackData = [
            'title' => '', // Empty title should fail validation
            'artist' => 'New Artist',
            'duration' => 200
        ];

        // Create mock validation errors
        $violation = $this->createMock(ConstraintViolation::class);
        $violation->method('getPropertyPath')->willReturn('title');
        $violation->method('getMessage')->willReturn('Title is required');

        $violationList = new ConstraintViolationList([$violation]);

        // Configure validator mock to return errors
        $this->validator->expects($this->once())
            ->method('validate')
            ->willReturn($violationList);

        // Entity manager should not be called
        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->expects($this->never())->method('flush');

        // Call the service method
        $result = $this->trackService->createTrack($trackData);

        // Assert the result
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('title', $result['errors']);
        $this->assertEquals('Title is required', $result['errors']['title']);
    }

    public function testUpdateTrackSuccess(): void
    {
        // Create existing track
        $existingTrack = new Track();
        $existingTrack->setTitle('Old Title');
        $existingTrack->setArtist('Old Artist');
        $existingTrack->setDuration(180);

        // Test data for update
        $updateData = [
            'title' => 'Updated Title',
            'artist' => 'Updated Artist',
            'duration' => 240
        ];

        // Configure repository mock
        $this->trackRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($existingTrack);

        // Configure validator mock
        $this->validator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        // Configure entity manager mock
        $this->entityManager->expects($this->once())
            ->method('flush');

        // Call the service method
        $result = $this->trackService->updateTrack(1, $updateData);

        // Assert the result
        $this->assertTrue($result['success']);
        $this->assertInstanceOf(Track::class, $result['track']);
        $this->assertEquals($updateData['title'], $result['track']->getTitle());
        $this->assertEquals($updateData['artist'], $result['track']->getArtist());
        $this->assertEquals($updateData['duration'], $result['track']->getDuration());
    }

    public function testUpdateTrackNotFound(): void
    {
        // Configure repository mock to return null (track not found)
        $this->trackRepository->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        // Validator and entity manager should not be called
        $this->validator->expects($this->never())->method('validate');
        $this->entityManager->expects($this->never())->method('flush');

        // Call the service method
        $result = $this->trackService->updateTrack(999, ['title' => 'New Title']);

        // Assert the result
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
        $this->assertContains('Track not found', $result['errors']);
    }

    public function testUpdateTrackValidationFailure(): void
    {
        // Create existing track
        $existingTrack = new Track();
        $existingTrack->setTitle('Old Title');
        $existingTrack->setArtist('Old Artist');
        $existingTrack->setDuration(180);

        // Test data with invalid values
        $updateData = [
            'duration' => -10 // Negative duration should fail validation
        ];

        // Create mock validation errors
        $violation = $this->createMock(ConstraintViolation::class);
        $violation->method('getPropertyPath')->willReturn('duration');
        $violation->method('getMessage')->willReturn('Duration must be a positive number');

        $violationList = new ConstraintViolationList([$violation]);

        // Configure repository mock
        $this->trackRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($existingTrack);

        // Configure validator mock
        $this->validator->expects($this->once())
            ->method('validate')
            ->willReturn($violationList);

        // Entity manager flush should not be called
        $this->entityManager->expects($this->never())->method('flush');

        // Call the service method
        $result = $this->trackService->updateTrack(1, $updateData);

        // Assert the result
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('duration', $result['errors']);
        $this->assertEquals('Duration must be a positive number', $result['errors']['duration']);
    }
}