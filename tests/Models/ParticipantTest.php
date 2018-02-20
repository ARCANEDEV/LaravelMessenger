<?php namespace Arcanedev\LaravelMessenger\Tests\Models;

use Arcanedev\LaravelMessenger\Models\Discussion;
use Arcanedev\LaravelMessenger\Models\Participant;
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
    public function it_can_create_a_participant()
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Participant $participant */
        $participant = $this->factory->create(Participant::class);

        static::assertInstanceOf(Participant::class, $participant);
        static::assertInstanceOf(User::class, $participant->user);
        static::assertEquals($participant->user_id, $participant->user->id);
    }

    /** @test */
    public function it_can_associate_discussion_to_participant()
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Participant $participant */
        $participant = $this->factory->create(Participant::class, ['discussion_id' => 0]);

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
