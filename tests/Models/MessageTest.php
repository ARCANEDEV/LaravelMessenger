<?php namespace Arcanedev\LaravelMessenger\Tests\Models;

use Arcanedev\LaravelMessenger\Models\Discussion;
use Arcanedev\LaravelMessenger\Models\Message;
use Arcanedev\LaravelMessenger\Models\Participant;
use Arcanedev\LaravelMessenger\Tests\TestCase;

/**
 * Class     MessageTest
 *
 * @package  Arcanedev\LaravelMessenger\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MessageTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    /** @test */
    public function it_can_save_message_to_discussion()
    {
        /**
         * @var \Arcanedev\LaravelMessenger\Models\Message     $message
         * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion
         */
        $message    = $this->factory->make(Message::class);
        $discussion = $this->factory->create(Discussion::class);

        $discussion->messages()->save($message);

        $this->assertInstanceOf(Discussion::class, $message->discussion);
        $this->assertSame($discussion->id, $message->discussion->id);
    }

    /** @test */
    public function it_can_save_multiple_messages_to_discussion()
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $messages   = $this->factory->of(Message::class)->times(3)->make();
        $discussion = $this->factory->create(Discussion::class);

        $discussion->messages()->saveMany($messages);

        $this->assertCount(3, $discussion->messages);
    }

    /** @test */
    public function it_can_get_author()
    {
        /**
         * @var \Arcanedev\LaravelMessenger\Models\Message     $message
         * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion
         */
        $message    = $this->factory->make(Message::class, ['user_id' => 2]);
        $discussion = $this->factory->create(Discussion::class);

        $discussion->messages()->save($message);

        $this->assertEquals(2, $message->author->id);
    }

    /** @test */
    public function it_can_get_the_recipients()
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Message $message */
        $discussion = $this->factory->create(Discussion::class);
        $discussion->messages()->save(
            $message = $this->factory->make(Message::class, ['user_id' => 1])
        );
        $discussion->participants()->saveMany([
            $this->factory->make(Participant::class, ['user_id' => 1]),
            $this->factory->make(Participant::class, ['user_id' => 2]),
            $this->factory->make(Participant::class, ['user_id' => 3])
        ]);

        $this->assertTrue($message->participants > $message->recipients);
        $this->assertCount(3, $message->participants);
        $this->assertCount(2, $message->recipients);

        foreach ($message->recipients as $recipient) {
            /** @var \Arcanedev\LaravelMessenger\Models\Participant $recipient */
            $this->assertInstanceOf(Participant::class, $recipient);
        }
    }
}
