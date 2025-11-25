<?php

namespace App\Tests\Entity;

use App\Entity\Track;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TrackTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testGettersAndSetters(): void
    {
        $track = new Track();

        // Test title
        $title = 'Test Track Title';
        $track->setTitle($title);
        $this->assertEquals($title, $track->getTitle());

        // Test artist
        $artist = 'Test Artist';
        $track->setArtist($artist);
        $this->assertEquals($artist, $track->getArtist());

        // Test duration
        $duration = 180;
        $track->setDuration($duration);
        $this->assertEquals($duration, $track->getDuration());

        // Test ISRC
        $isrc = 'US-ABC-12-12345';
        $track->setIsrc($isrc);
        $this->assertEquals($isrc, $track->getIsrc());

        // Test null ISRC
        $track->setIsrc(null);
        $this->assertNull($track->getIsrc());
    }

    public function testIdIsNullByDefault(): void
    {
        $track = new Track();
        $this->assertNull($track->getId());
    }

    public function testValidTrackEntity(): void
    {
        $track = new Track();
        $track->setTitle('Valid Title');
        $track->setArtist('Valid Artist');
        $track->setDuration(240);
        $track->setIsrc('US-ABC-12-12345');

        $errors = $this->validator->validate($track);
        $this->assertCount(0, $errors);
    }

    public function testInvalidTrackEntity(): void
    {
        $track = new Track();
        // Missing required fields

        $errors = $this->validator->validate($track);
        $this->assertGreaterThan(0, $errors->count());

        // Test specific validation errors
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }

        $this->assertArrayHasKey('title', $errorMessages);
        $this->assertArrayHasKey('artist', $errorMessages);
        $this->assertArrayHasKey('duration', $errorMessages);
    }

    public function testInvalidIsrcFormat(): void
    {
        $track = new Track();
        $track->setTitle('Valid Title');
        $track->setArtist('Valid Artist');
        $track->setDuration(240);
        $track->setIsrc('invalid-isrc'); // Invalid format

        $errors = $this->validator->validate($track);
        $this->assertGreaterThan(0, $errors->count());

        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }

        $this->assertArrayHasKey('isrc', $errorMessages);
    }

    public function testNegativeDuration(): void
    {
        $track = new Track();
        $track->setTitle('Valid Title');
        $track->setArtist('Valid Artist');
        $track->setDuration(-10); // Negative duration

        $errors = $this->validator->validate($track);
        $this->assertGreaterThan(0, $errors->count());

        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }

        $this->assertArrayHasKey('duration', $errorMessages);
    }
}
