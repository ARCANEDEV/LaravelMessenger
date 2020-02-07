<?php

declare(strict_types=1);

namespace Arcanedev\LaravelMessenger\Tests\Models;

use Arcanedev\LaravelMessenger\Models\{Discussion, Message, Participation};
use Arcanedev\LaravelMessenger\Tests\Stubs\Models\User;
use Arcanedev\LaravelMessenger\Tests\TestCase;
use Illuminate\Support\{Carbon, Collection};

/**
 * Class     DiscussionTest
 *
 * @package  Arcanedev\LaravelMessenger\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DiscussionTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_create_discussion(): void
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion = $this->factory->create(Discussion::class, [
            'subject' => 'Hello world'
        ]);

        static::assertSame('Hello world', $discussion->subject);
        static::assertCount(0, $discussion->messages);
        static::assertCount(0, $discussion->participations);
    }

    /** @test */
    public function it_can_get_the_creator(): void
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion = $this->factory->create(Discussion::class);
        $discussion->messages()->save(
            $this->factory->make(Message::class)
        );

        static::assertInstanceOf(User::class, $discussion->creator);
        static::assertEquals(1, $discussion->creator->id);
    }

    /** @test */
    public function it_can_add_a_participant(): void
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $user         = $this->users->first();
        $discussion   = $this->factory->create(Discussion::class);
        $participant  = $discussion->addParticipant($user);

        static::assertTrue($discussion->hasParticipation($user));
        static::assertCount(1, $discussion->participations);
        static::assertEquals(
            $discussion->participations->first()->id,
            $participant->id
        );
    }

    /** @test */
    public function it_can_add_participant_without_duplication(): void
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $user       = $this->users->first();
        $discussion = $this->factory->create(Discussion::class);

        for ($i = 0; $i < 5; $i++) {
            $participant = $discussion->addParticipant($user);

            static::assertCount(1, $discussion->participations);
            static::assertEquals($participant->id, $discussion->participations->first()->id);
        }
    }

    /** @test */
    public function it_can_add_multiple_participants(): void
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion   = $this->factory->create(Discussion::class);
        $participants = $discussion->addParticipants($this->users);

        static::assertSame(
            $discussion->participations->count(),
            $participants->count()
        );

        static::assertSame(
            $discussion->participations->pluck('participable_id')->toArray(),
            $this->users->pluck('id')->toArray()
        );
    }

    /** @test */
    public function it_can_remove_a_participant(): void
    {
        /** @var  \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion  = $this->factory->create(Discussion::class);
        $discussion->addParticipant(
            $user = $this->users->first()
        );

        static::assertCount(1, $discussion->participations);

        $deleted = $discussion->removeParticipant($user);

        static::assertSame(1, $deleted);
        static::assertCount(0, $discussion->participations);
    }

    /** @test */
    public function it_can_remove_a_participant_without_reloading_the_collection(): void
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion = $this->factory->create(Discussion::class);
        $discussion->addParticipant(
            $user = $this->users->first()
        );

        static::assertCount(1, $discussion->participations);

        $deleted = $discussion->removeParticipant($user, false);

        static::assertSame(1, $deleted);
        static::assertCount(1, $discussion->participations);
    }

    /** @test */
    public function it_can_remove_multiple_participants(): void
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion   = $this->factory->create(Discussion::class);
        $participants = $discussion->addParticipants($this->users);

        static::assertCount($this->users->count(), $discussion->participations);
        static::assertSame(
            $discussion->participations->count(),
            $participants->count()
        );

        $deleted = $discussion->removeParticipants($this->users);

        static::assertSame($this->users->count(), $deleted);
        static::assertCount(0, $discussion->participations);
    }

    /** @test */
    public function it_can_remove_multiple_participants_without_reloading_the_collection(): void
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion   = $this->factory->create(Discussion::class);
        $participants = $discussion->addParticipants($this->users);

        static::assertCount($this->users->count(), $discussion->participations);
        static::assertSame(
            $participants->count(),
            $discussion->participations->count()
        );

        $deleted = $discussion->removeParticipants($this->users, false);

        static::assertSame($this->users->count(), $deleted);
        static::assertCount(
            $participants->count(),
            $discussion->participations
        );
    }

    /** @test */
    public function it_can_get_trashed_participants(): void
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion   = $this->factory->create(Discussion::class);
        $participants = $discussion->addParticipants($this->users);

        static::assertCount($this->users->count(), $discussion->participations);
        static::assertSame(
            $participants->count(),
            $discussion->participations->count()
        );

        $deleted = $discussion->removeParticipants($this->users, false);

        static::assertSame($this->users->count(), $deleted);
        static::assertCount($participants->count(), $discussion->participations);

        $trashed = $discussion->getTrashedParticipations();

        static::assertCount($deleted, $trashed);
    }

    /** @test */
    public function it_can_restore_all_trashed_participants(): void
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion   = $this->factory->create(Discussion::class);
        $discussion->addParticipants($this->users);

        static::assertCount($this->users->count(), $discussion->participations);

        $deleted = $discussion->removeParticipants($this->users);
        $trashed = $discussion->getTrashedParticipations();

        static::assertCount($deleted, $trashed);
        static::assertTrue($discussion->participations->isEmpty());

        $restored = $discussion->restoreAllParticipations();

        static::assertSame($deleted, $restored);
        static::assertFalse($discussion->participations->isEmpty());
    }

    /** @test */
    public function it_can_see_if_discussion_is_unread_by_user(): void
    {
        /**
         * @var \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $user
         * @var \Arcanedev\LaravelMessenger\Models\Discussion        $discussion
         */
        $user       = $this->users->first();
        $discussion = $this->factory->create(Discussion::class, ['updated_at' => Carbon::yesterday()]);
        $discussion->participations()->save(
            $this->factory->make(Participation::class, ['last_read' => Carbon::now()])
        );

        static::assertFalse($discussion->isUnread($user));

        $discussion = $this->factory->create(Discussion::class, ['subject' => 'Second Thread', 'updated_at' => Carbon::now()]);
        $discussion->participations()->save(
            $this->factory->make(Participation::class, ['last_read' => Carbon::yesterday()])
        );

        static::assertTrue($discussion->isUnread($user));
    }

    /** @test */
    public function it_can_check_if_has_users_and_participants(): void
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion   = $this->factory->create(Discussion::class);
        $participants = $this->users->map(function (User $user) {
            return $this->factory->make(Participation::class, [
                'participable_type' => $user->getMorphClass(),
                'participable_id'   => $user->getKey(),
            ]);
        });
        $discussion->participations()->saveMany($participants);

        $this->users->each(function(User $user) use ($discussion) {
            static::assertTrue($discussion->hasParticipation($user));
        });

        static::assertFalse($discussion->hasParticipation(
            $this->factory->create(User::class)
        ));
    }

    /** @test */
    public function it_can_get_a_participant_by_user_id(): void
    {
        /** @var  \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $user       = $this->users->first();
        $discussion = $this->factory->create(Discussion::class);
        $discussion->participations()->save(
            $this->factory->make(Participation::class)
        );

        /** @var  \Arcanedev\LaravelMessenger\Models\Participation  $participant */
        $participant = $discussion->getParticipationByParticipable($user);

        static::assertInstanceOf(Participation::class, $participant);
        static::assertSame($user->getMorphClass(), $participant->participable_type);
        static::assertSame($user->getKey(), $participant->participable_id);
    }

    /** @test */
    public function it_can_get_participants_string(): void
    {
        /** @var  \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion   = $this->factory->create(Discussion::class);
        $participants = $discussion->addParticipants($this->users);

        $rendered = $discussion->participationsString();
        static::assertStringContainsString(', ', $rendered);
        static::assertCount($participants->count(), explode(', ', $rendered));
    }

    /** @test */
    public function it_can_get_participants_custom_string(): void
    {
        /** @var  \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion = $this->factory->create(Discussion::class);
        $discussion->addParticipants($this->users);

        static::assertSame(
            '#1 from [Arcanedev\LaravelMessenger\Tests\Stubs\Models\User], #2 from [Arcanedev\LaravelMessenger\Tests\Stubs\Models\User], #3 from [Arcanedev\LaravelMessenger\Tests\Stubs\Models\User]',
            $discussion->participationsString(function (Participation $participant) {
                return '#'.$participant->participable_id.' from ['.$participant->participable_type.']';
            })
        );
    }

    /** @test */
    public function it_can_mark_last_read(): void
    {
        /** @var  \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion  = $this->factory->create(Discussion::class);
        $participant = $discussion->addParticipant(
            $user = $this->users->first()
        );

        static::assertCount(1, $discussion->participations);
        static::assertEquals(
            $discussion->participations->first()->id,
            $participant->id
        );

        /** @var  \Arcanedev\LaravelMessenger\Models\Participation  $participant */
        foreach ($discussion->participations as $participant) {
            static::assertNull($participant->last_read);
            $discussion->markAsRead($participant->participable);
        }

        $discussion->load(['participations']);

        /** @var  \Arcanedev\LaravelMessenger\Models\Participation  $participant */
        foreach ($discussion->participations as $participant) {
            static::assertNotNull($participant->last_read);
        }
    }

    /** @test */
    public function it_can_skip_mark_last_read_if_participant_not_found(): void
    {
        /** @var  \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $user       = $this->users->first();
        $discussion = $this->factory->create(Discussion::class);

        static::assertFalse($discussion->markAsRead($user));
    }

    /** @test */
    public function it_can_check_participant_if_has_not_marked_as_read(): void
    {
        /** @var  \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion  = $this->factory->create(Discussion::class);
        $participant = $discussion->addParticipant(
            $user = $this->users->first()
        );

        static::assertCount(1, $discussion->participations);

        static::assertTrue($discussion->isUnread($participant->participable));
        static::assertTrue($discussion->isUnread($user));

        $discussion->markAsRead($user);

        static::assertFalse($discussion->isUnread($participant->participable));
        static::assertFalse($discussion->isUnread($user));
    }

    /** @test */
    public function it_can_get_the_latest_message(): void
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion = $this->factory->create(Discussion::class);

        $discussion->messages()->save($this->factory->make(Message::class));
        sleep(1);
        $discussion->messages()->save($this->factory->make(Message::class));
        sleep(1);
        $discussion->messages()->save($this->factory->make(Message::class));

        static::assertCount(3, $discussion->messages);

        $latestMessage = $discussion->latest_message;

        static::assertInstanceOf(Message::class, $latestMessage);
        static::assertEquals(3, $latestMessage->id);
    }

    /** @test */
    public function it_get_all_latest_discussions(): void
    {
        $now = Carbon::now();
        for ($i = 0; $i < 5; $i++) {
            $discussion = $this->factory->create(Discussion::class, [
                'created_at' => $now,
            ]);
            $discussion->messages()->save($this->factory->make(Message::class, ['discussion_id' => null]));
            sleep(1);
        }

        $discussions = Discussion::getLatest();

        static::assertCount(5, $discussions);

        /**
         * @var \Arcanedev\LaravelMessenger\Models\Discussion  $first
         * @var \Arcanedev\LaravelMessenger\Models\Discussion  $last
         */
        $first = $discussions->first();
        $last  = $discussions->last();

        static::assertTrue($first->created_at->eq($last->created_at));
        static::assertTrue($first->updated_at->gt($last->updated_at));
    }

    /** @test */
    public function it_can_get_discussions_by_subject(): void
    {
        $attributes = [
            ['subject' => 'Hello World'],
            ['subject' => 'Hello Laravel'],
            ['subject' => 'Hi there!'],
        ];

        foreach ($attributes as $attribute) {
            $this->factory->create(Discussion::class, $attribute);
        }

        static::assertCount(2, Discussion::getBySubject('Hello'));
        static::assertCount(1, Discussion::getBySubject('Hi'));
    }

    /** @test */
    public function it_can_get_discussions_by_strict_subject(): void
    {
        $attributes = [
            ['subject' => 'Hello World'],
            ['subject' => 'Hello Laravel'],
        ];

        foreach ($attributes as $attribute) {
            $this->factory->create(Discussion::class, $attribute);
        }

        static::assertCount(0, Discussion::getBySubject('Hello', true));
        static::assertCount(1, Discussion::getBySubject('Hello Laravel', true));
    }

    /** @test */
    public function it_can_get_users_ids(): void
    {
        /**
         * @var \Illuminate\Database\Eloquent\Collection       $users
         * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion
         */
        $users      = $this->factory->of(Discussion::class)->times(3)->create();
        $discussion = $this->factory->create(Discussion::class);
        $discussion->addParticipants($users);

        $participables = $discussion->getParticipables();

        static::assertInstanceOf(Collection::class, $participables);
        static::assertCount($users->count(), $participables);
    }

    /** @test */
    public function it_can_get_all_discussions_between_specific_users(): void
    {
        /**
         * @var \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $userOne
         * @var \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $userTwo
         * @var \Arcanedev\LaravelMessenger\Models\Discussion        $discussionOne
         * @var \Arcanedev\LaravelMessenger\Models\Discussion        $discussionTwo
         */
        $userOne       = $this->users->get(0);
        $userTwo       = $this->users->get(1);
        $discussionOne = $this->factory->create(Discussion::class);

        $discussionOne->participations()->saveMany([
            $this->factory->make(Participation::class),
            $this->factory->make(Participation::class, ['participable_id' => $userTwo->getKey()]),
        ]);

        $discussionTwo = $this->factory->create(Discussion::class);
        $discussionTwo->participations()->save(
            $this->factory->make(Participation::class)
        );

        $discussions = Discussion::between([$userOne, $userTwo])->get();

        static::assertCount(1, $discussions);
    }

    /** @test */
    public function it_can_get_all_discussions_for_a_user(): void
    {
        /**
         * @var \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $user
         * @var \Arcanedev\LaravelMessenger\Models\Discussion        $discussionOne
         * @var \Arcanedev\LaravelMessenger\Models\Discussion        $discussionTwo
         */
        $user          = $this->users->first();
        $discussionOne = $this->factory->create(Discussion::class);
        $discussionOne->participations()->save(
            $this->factory->make(Participation::class)
        );

        $discussionTwo  = $this->factory->create(Discussion::class, ['subject' => 'Second Thread']);
        $discussionTwo->participations()->save(
            $this->factory->make(Participation::class)
        );

        $discussions = Discussion::forUser($user)->with(['participations'])->get();

        static::assertCount(2, $discussions);
    }

    /** @test */
    public function it_can_get_all_users_for_a_discussion(): void
    {
        /**
         * @var  \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $userOne
         * @var  \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $userTwo
         * @var  \Arcanedev\LaravelMessenger\Models\Discussion        $discussion
         */
        $userOne    = $this->users->get(0);
        $userTwo    = $this->users->get(1);
        $discussion = $this->factory->create(Discussion::class);

        $discussion->participations()->saveMany([
            $this->factory->make(Participation::class),
            $this->factory->make(Participation::class, ['participable_id' => $userTwo->getKey()]),
        ]);

        $discussionUsers = $discussion->getParticipables();

        static::assertCount(2, $discussionUsers);
        static::assertSame([$userOne->id, $userTwo->id], $discussionUsers->pluck('id')->toArray());
    }

    /** @test */
    public function it_can_get_all_discussions_for_a_user_with_new_messages(): void
    {
        /**
         * @var  \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $user
         * @var  \Arcanedev\LaravelMessenger\Models\Discussion        $discussionOne
         * @var  \Arcanedev\LaravelMessenger\Models\Discussion        $discussionTwo
         */
        $user          = $this->users->first();
        $discussionOne = $this->factory->create(Discussion::class, ['updated_at' => Carbon::yesterday()]);
        $discussionOne->participations()->save(
            $this->factory->make(Participation::class, ['last_read' => Carbon::now()])
        );

        $discussionTwo = $this->factory->create(Discussion::class, ['subject' => 'Second Thread', 'updated_at' => Carbon::now()]);
        $discussionTwo->participations()->save(
            $this->factory->make(Participation::class, ['last_read' => Carbon::yesterday()])
        );

        $discussions = Discussion::forUserWithNewMessages($user)->get();

        static::assertCount(1, $discussions);
    }

    /** @test */
    public function it_should_get_all_unread_messages_for_user(): void
    {
        /**
         * @var  \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $userOne
         * @var  \Arcanedev\LaravelMessenger\Tests\Stubs\Models\User  $userTwo
         * @var  \Arcanedev\LaravelMessenger\Models\Discussion        $discussion
         */
        $userOne    = $this->factory->create(User::class);
        $userTwo    = $this->factory->create(User::class);
        $discussion = $this->factory->create(Discussion::class);
        $discussion->participations()->saveMany([
            $participantOne = $this->factory->make(Participation::class, [
                'participable_type' => $userOne->getMorphClass(),
                'participable_id'   => $userOne->getKey(),
            ]),
            $participantTwo = $this->factory->make(Participation::class, [
                'participable_type' => $userTwo->getMorphClass(),
                'participable_id'   => $userTwo->getKey(),
            ]),
        ]);
        $discussion->messages()->save(
            $this->factory->make(Message::class, ['body' => 'Message 1', 'created_at' => Carbon::now()])
        );
        $discussion->markAsRead($userTwo);
        // Simulate delay after last read
        sleep(1);

        $discussion->messages()->save(
            $this->factory->make(Message::class, ['body' => 'Message 2', 'created_at' => Carbon::now()])
        );

        $messages = $discussion->getUnreadMessages($userOne);

        static::assertInstanceOf(Collection::class, $messages);
        static::assertCount(2, $messages);
        static::assertEquals('Message 1', $messages->first()->body);

        $messages = $discussion->getUnreadMessages($userTwo);

        static::assertInstanceOf(Collection::class, $messages);
        static::assertCount(1, $messages);
        static::assertEquals('Message 2', $messages->first()->body);
    }

    /** @test */
    public function it_can_get_count_of_all_unread_messages_for_user(): void
    {
        /**
         * @var  \Arcanedev\LaravelMessenger\Models\Discussion     $discussion
         * @var  \Arcanedev\LaravelMessenger\Models\Participation  $participantOne
         * @var  \Arcanedev\LaravelMessenger\Models\Participation  $participantTwo
         */
        $discussion = $this->factory->create(Discussion::class);
        $discussion->participations()->saveMany([
            $participantOne = $this->factory->make(Participation::class),
            $participantTwo = $this->factory->make(Participation::class, ['participable_id' => 2]),
        ]);
        $discussion->messages()->save(
            $this->factory->make(Message::class, ['body' => 'Message 1', 'created_at' => Carbon::now()])
        );
        $discussion->markAsRead($participantTwo->participable);
        // Simulate delay after last read
        sleep(1);

        $discussion->messages()->save(
            $this->factory->make(Message::class, ['body' => 'Message 2', 'created_at' => Carbon::now()])
        );

        static::assertCount(2, $discussion->getUnreadMessages($participantOne->participable));
        static::assertCount(1, $discussion->getUnreadMessages($participantTwo->participable));

        // it must return empty unread messages collection with invalid participant id
        $messages = $discussion->getUnreadMessages(
            $this->factory->create(User::class)
        );

        static::assertInstanceOf(Collection::class, $messages);
        static::assertCount(0, $messages);
    }
}
