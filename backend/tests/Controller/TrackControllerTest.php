<?php

namespace App\Tests\Controller;

use App\Controller\TrackController;
use App\Entity\Track;
use App\Service\TrackService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackControllerTest extends TestCase
{
    private TrackService $trackService;
    private TrackController $trackController;

    protected function setUp(): void
    {
        $this->trackService = $this->createMock(TrackService::class);
        $this->trackController = new TrackController($this->trackService);
    }

    public function testGetTracks(): void
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

        $tracks = [$track1, $track2];

        // Configure mock service
        $this->trackService->expects($this->once())
            ->method('getAllTracks')
            ->willReturn($tracks);

        // Call the controller method
        $response = $this->trackController->getTracks();

        // Assert the response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        // Convert the response content to array for assertion
        $content = json_decode($response->getContent(), true);
        $this->assertCount(2, $content);
    }

    public function testCreateTrackSuccess(): void
    {
        // Create test data
        $trackData = [
            'title' => 'New Track',
            'artist' => 'New Artist',
            'duration' => 200
        ];

        // Create mock track
        $track = new Track();
        $track->setTitle($trackData['title']);
        $track->setArtist($trackData['artist']);
        $track->setDuration($trackData['duration']);

        // Create mock service response
        $serviceResponse = [
            'success' => true,
            'track' => $track
        ];

        // Create mock request
        $request = new Request([], [], [], [], [], [], json_encode($trackData));

        // Configure mock service
        $this->trackService->expects($this->once())
            ->method('createTrack')
            ->with($trackData)
            ->willReturn($serviceResponse);

        // Call the controller method
        $response = $this->trackController->createTrack($request);

        // Assert the response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        
        // Convert the response content to array for assertion
        $content = json_decode($response->getContent(), true);
        $this->assertEquals($trackData['title'], $content['title']);
        $this->assertEquals($trackData['artist'], $content['artist']);
        $this->assertEquals($trackData['duration'], $content['duration']);
    }

    public function testCreateTrackValidationFailure(): void
    {
        // Create test data with missing required fields
        $trackData = [
            'title' => '', // Empty title should fail validation
            'artist' => 'New Artist',
            'duration' => 200
        ];

        // Create mock service response with errors
        $serviceResponse = [
            'success' => false,
            'errors' => [
                'title' => 'Title is required'
            ]
        ];

        // Create mock request
        $request = new Request([], [], [], [], [], [], json_encode($trackData));

        // Configure mock service
        $this->trackService->expects($this->once())
            ->method('createTrack')
            ->with($trackData)
            ->willReturn($serviceResponse);

        // Call the controller method
        $response = $this->trackController->createTrack($request);

        // Assert the response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        
        // Convert the response content to array for assertion
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('title', $content['errors']);
        $this->assertEquals('Title is required', $content['errors']['title']);
    }

    public function testUpdateTrackSuccess(): void
    {
        // Create test data
        $trackId = 1;
        $updateData = [
            'title' => 'Updated Track',
            'artist' => 'Updated Artist',
            'duration' => 240
        ];

        // Create mock track
        $track = new Track();
        $track->setTitle($updateData['title']);
        $track->setArtist($updateData['artist']);
        $track->setDuration($updateData['duration']);

        // Create mock service response
        $serviceResponse = [
            'success' => true,
            'track' => $track
        ];

        // Create mock request
        $request = new Request([], [], [], [], [], [], json_encode($updateData));

        // Configure mock service
        $this->trackService->expects($this->once())
            ->method('updateTrack')
            ->with($trackId, $updateData)
            ->willReturn($serviceResponse);

        // Call the controller method
        $response = $this->trackController->updateTrack($trackId, $request);

        // Assert the response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        // Convert the response content to array for assertion
        $content = json_decode($response->getContent(), true);
        $this->assertEquals($updateData['title'], $content['title']);
        $this->assertEquals($updateData['artist'], $content['artist']);
        $this->assertEquals($updateData['duration'], $content['duration']);
    }

    public function testUpdateTrackNotFound(): void
    {
        // Create test data
        $trackId = 999; // Non-existent ID
        $updateData = [
            'title' => 'Updated Track'
        ];

        // Create mock service response for not found
        $serviceResponse = [
            'success' => false,
            'errors' => ['Track not found']
        ];

        // Create mock request
        $request = new Request([], [], [], [], [], [], json_encode($updateData));

        // Configure mock service
        $this->trackService->expects($this->once())
            ->method('updateTrack')
            ->with($trackId, $updateData)
            ->willReturn($serviceResponse);

        // Call the controller method
        $response = $this->trackController->updateTrack($trackId, $request);

        // Assert the response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        
        // Convert the response content to array for assertion
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Track not found', $content['error']);
    }

    public function testUpdateTrackValidationFailure(): void
    {
        // Create test data
        $trackId = 1;
        $updateData = [
            'duration' => -10 // Negative duration should fail validation
        ];

        // Create mock service response with validation errors
        $serviceResponse = [
            'success' => false,
            'errors' => [
                'duration' => 'Duration must be a positive number'
            ]
        ];

        // Create mock request
        $request = new Request([], [], [], [], [], [], json_encode($updateData));

        // Configure mock service
        $this->trackService->expects($this->once())
            ->method('updateTrack')
            ->with($trackId, $updateData)
            ->willReturn($serviceResponse);

        // Call the controller method
        $response = $this->trackController->updateTrack($trackId, $request);

        // Assert the response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        
        // Convert the response content to array for assertion
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('duration', $content['errors']);
        $this->assertEquals('Duration must be a positive number', $content['errors']['duration']);
    }
}