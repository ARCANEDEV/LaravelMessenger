<?php namespace Arcanedev\LaravelMessenger\Tests\Traits;

use Arcanedev\LaravelMessenger\Models\Discussion;
use Arcanedev\LaravelMessenger\Models\Message;
use Arcanedev\LaravelMessenger\Models\Participant;
use Arcanedev\LaravelMessenger\Tests\Stubs\Models\User;
use Arcanedev\LaravelMessenger\Tests\TestCase;
use Carbon\Carbon;

/**
 * Class     MessagableTest
 *
 * @package  Arcanedev\LaravelMessenger\Tests\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MessagableTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    /** @test */
    public function it_should_get_all_discussions_with_new_messages()
    {
        /** @var \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $user */
        $user = $this->factory->create(User::class, [
            'name'  => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $discussionOne = $this->factory->create(Discussion::class);

        $discussionOne->participants()->saveMany([
            $this->factory->make(Participant::class, ['user_id' => $user->id, 'last_read' => Carbon::yesterday()]),
            $this->factory->make(Participant::class, ['user_id' => 2])
        ]);

        $discussionOne->messages()->saveMany([
            $this->factory->make(Message::class, ['user_id' => 2]),
        ]);

        $discussionTwo = factory(Discussion::class)->create();
        $discussionTwo->participants()->saveMany([
            $this->factory->make(Participant::class, ['user_id' => 3, 'last_read' => Carbon::yesterday()]),
            $this->factory->make(Participant::class, ['user_id' => 2])
        ]);
        $discussionTwo->messages()->saveMany([
            $this->factory->make(Message::class, ['user_id' => 2])
        ]);

        $discussions = $user->discussionsWithNewMessages();

        $this->assertSame(1, $discussions[0]);
        $this->assertSame(1, $user->newMessagesCount());
    }

    /** @test */
    public function it_should_get_participant_discussions()
    {
        /** @var \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $user */
        $user = $this->factory->create(User::class, [
            'name'  => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $discussion = $this->factory->create(Discussion::class);
        $discussion->participants()->saveMany([
            $this->factory->make(Participant::class, ['user_id' => $user->id]),
            $this->factory->make(Participant::class, ['user_id' => 2])
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->discussions);
        $this->assertCount(1, $user->discussions);

        $firstDiscussion = $user->discussions->first();

        $this->assertInstanceOf(Discussion::class, $firstDiscussion);
    }

    /** @test */
    public function it_can_get_all_messages()
    {
        $count = 3;

        /** @var \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $user */
        $user = $this->factory->create(User::class, [
            'name'  => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $this->assertCount(0, $user->messages);

        $this->factory->of(Message::class)->times($count)->create([
            'user_id' => $user->id,
        ]);

        $user->load(['messages']);

        $this->assertCount($count, $user->messages);
    }

    /** @test */
    public function it_can_get_participants()
    {
        $count = 5;

        /** @var \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $user */
        $user = $this->factory->create(User::class, [
            'name'  => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $this->assertCount(0, $user->participants);

        $this->factory->of(Participant::class)->times($count)->create([
            'user_id' => $user->id
        ]);

        $user->load(['participants']);

        $this->assertCount($count, $user->participants);
        foreach ($user->participants as $participation) {
            /** @var \Arcanedev\LaravelMessenger\Models\Participant $participation */
            $this->assertSame($user->id, $participation->user_id);
        }
    }
}
