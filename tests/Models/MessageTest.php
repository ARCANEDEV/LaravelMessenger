<?php

declare(strict_types=1);

namespace Arcanedev\LaravelMessenger\Tests\Models;

use Arcanedev\LaravelMessenger\Models\{Discussion, Message, Participation};
use Arcanedev\LaravelMessenger\Tests\TestCase;

/**
 * Class     MessageTest
 *
 * @package  Arcanedev\LaravelMessenger\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MessageTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_save_message_to_discussion(): void
    {
        /**
         * @var \Arcanedev\LaravelMessenger\Models\Message     $message
         * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion
         */
        $message    = $this->factory->make(Message::class);
        $discussion = $this->factory->create(Discussion::class);

        $discussion->messages()->save($message);

        static::assertInstanceOf(Discussion::class, $message->discussion);
        static::assertSame($discussion->id, $message->discussion->id);
    }

    /** @test */
    public function it_can_save_multiple_messages_to_discussion(): void
    {
        /** @var  \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $messages   = $this->factory->of(Message::class)->times(3)->make();
        $discussion = $this->factory->create(Discussion::class);

        $discussion->messages()->saveMany($messages);

        static::assertCount(3, $discussion->messages);
    }

    /** @test */
    public function it_can_get_author(): void
    {
        /**
         * @var  \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $user
         * @var  \Arcanedev\LaravelMessenger\Models\Message           $message
         * @var  \Arcanedev\LaravelMessenger\Models\Discussion        $discussion
         */
        $user       = $this->users->get(1);
        $message    = $this->factory->make(Message::class, [
            'participable_type' => $user->getMorphClass(),
            'participable_id'   => $user->getKey(),
        ]);
        $discussion = $this->factory->create(Discussion::class);

        $discussion->messages()->save($message);

        static::assertEquals(2, $message->author->id);
    }

    /** @test */
    public function it_can_get_the_recipients(): void
    {
        /**
         * @var  \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $userOne
         * @var  \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $userTwo
         * @var  \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $userThree
         * @var  \Arcanedev\LaravelMessenger\Models\Discussion        $discussion
         * @var  \Arcanedev\LaravelMessenger\Models\Message           $message
         */
        $userOne    = $this->users->get(0);
        $userTwo    = $this->users->get(1);
        $userThree  = $this->users->get(2);

        $discussion = $this->factory->create(Discussion::class);
        $discussion->messages()->save(
            $message = $this->factory->make(Message::class, [
                'participable_type' => $userOne->getMorphClass(),
                'participable_id'   => $userOne->getKey(),
            ])
        );
        $discussion->participations()->saveMany([
            $this->factory->make(Participation::class, [
                'participable_type' => $userOne->getMorphClass(),
                'participable_id'   => $userOne->getKey(),
            ]),
            $this->factory->make(Participation::class, [
                'participable_type' => $userTwo->getMorphClass(),
                'participable_id'   => $userTwo->getKey(),
            ]),
            $this->factory->make(Participation::class, [
                'participable_type' => $userThree->getMorphClass(),
                'participable_id'   => $userThree->getKey(),
            ])
        ]);

        static::assertTrue($message->participations > $message->recipients);
        static::assertCount(3, $message->participations);
        static::assertCount(2, $message->recipients);

        foreach ($message->recipients as $recipient) {
            /** @var \Arcanedev\LaravelMessenger\Models\Participation $recipient */
            static::assertInstanceOf(Participation::class, $recipient);
        }
    }
}
