<?php

declare(strict_types=1);

namespace Arcanedev\LaravelMessenger\Tests\Models;

use Arcanedev\LaravelMessenger\Models\{Discussion, Participation};
use Arcanedev\LaravelMessenger\Tests\Stubs\Models\User;
use Arcanedev\LaravelMessenger\Tests\TestCase;

/**
 * Class     ParticipantTest
 *
 * @package  Arcanedev\LaravelMessenger\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ParticipantTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_create_a_participant(): void
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Participation $participant */
        $participant = $this->factory->create(Participation::class);

        static::assertInstanceOf(Participation::class, $participant);
        static::assertInstanceOf(User::class, $participant->participable);
        static::assertEquals($participant->participable_id, $participant->participable->id);
    }

    /** @test */
    public function it_can_associate_discussion_to_participant(): void
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Participation $participant */
        $participant = $this->factory->create(Participation::class, ['discussion_id' => 0]);

        static::assertNull($participant->discussion);

        $discussion = $this->factory->create(Discussion::class, [
            'subject' => 'Hello World!',
        ]);
        $participant->discussion()->associate($discussion);

        static::assertSame($discussion->id, $participant->discussion_id);
        static::assertInstanceOf(Discussion::class, $participant->discussion);
        static::assertSame($discussion->id, $participant->discussion->id);
        static::assertSame('Hello World!', $participant->discussion->subject);
    }
}
