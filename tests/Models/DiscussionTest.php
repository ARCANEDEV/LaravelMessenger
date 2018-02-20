<?php namespace Arcanedev\LaravelMessenger\Tests\Models;

use Arcanedev\LaravelMessenger\Models\Discussion;
use Arcanedev\LaravelMessenger\Models\Message;
use Arcanedev\LaravelMessenger\Models\Participant;
use Arcanedev\LaravelMessenger\Tests\Stubs\Models\User;
use Arcanedev\LaravelMessenger\Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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
    public function it_can_create_discussion()
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion = $this->factory->create(Discussion::class, [
            'subject' => 'Hello world'
        ]);

        static::assertSame('Hello world', $discussion->subject);
        static::assertCount(0, $discussion->messages);
        static::assertCount(0, $discussion->participants);
    }

    /** @test */
    public function it_can_get_the_creator()
    {
        $userId = 1;
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion = $this->factory->create(Discussion::class);
        $discussion->messages()->save(
            $this->factory->make(Message::class, ['user_id' => $userId])
        );

        static::assertInstanceOf(User::class, $discussion->creator);
        static::assertEquals($userId, $discussion->creator->id);
    }

    /** @test */
    public function it_can_add_a_participant()
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $userId      = 1;
        $discussion  = $this->factory->create(Discussion::class);
        $participant = $discussion->addParticipant($userId);

        static::assertTrue($discussion->hasParticipant($userId));
        static::assertCount(1, $discussion->participants);
        static::assertEquals(
            $discussion->participants->first()->id,
            $participant->id
        );
    }

    /** @test */
    public function it_can_add_participant_without_duplication()
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion  = $this->factory->create(Discussion::class);

        for ($i = 0; $i < 5; $i++) {
            $participant = $discussion->addParticipant(1);

            static::assertCount(1, $discussion->participants);
            static::assertEquals($participant->id, $discussion->participants->first()->id);
        }
    }

    /** @test */
    public function it_can_add_multiple_participants()
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $ids          = [1, 2, 3];
        $discussion   = $this->factory->create(Discussion::class);
        $participants = $discussion->addParticipants($ids);

        static::assertSame(
            $discussion->participants->count(),
            $participants->count()
        );

        foreach ($discussion->participants as $participant) {
            static::assertTrue(in_array($participant->id, $ids));
        }
    }

    /** @test */
    public function it_can_remove_a_participant()
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion  = $this->factory->create(Discussion::class);
        $discussion->addParticipant(1);

        static::assertCount(1, $discussion->participants);

        $deleted = $discussion->removeParticipant(1);

        static::assertSame(1, $deleted);
        static::assertCount(0, $discussion->participants);
    }

    /** @test */
    public function it_can_remove_a_participant_without_reloading_the_collection()
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion  = $this->factory->create(Discussion::class);
        $discussion->addParticipant(1);

        static::assertCount(1, $discussion->participants);

        $deleted = $discussion->removeParticipant(1, false);

        static::assertSame(1, $deleted);
        static::assertCount(1, $discussion->participants);
    }

    /** @test */
    public function it_can_remove_multiple_participants()
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $ids          = [1, 2, 3];
        $discussion   = $this->factory->create(Discussion::class);
        $participants = $discussion->addParticipants($ids);

        static::assertCount(count($ids), $discussion->participants);
        static::assertSame(
            $discussion->participants->count(),
            $participants->count()
        );

        $deleted = $discussion->removeParticipants($ids);

        static::assertSame(count($ids), $deleted);
        static::assertCount(0, $discussion->participants);
    }

    /** @test */
    public function it_can_remove_multiple_participants_without_reloading_the_collection()
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $ids          = [1, 2, 3];
        $discussion   = $this->factory->create(Discussion::class);
        $participants = $discussion->addParticipants($ids);

        static::assertCount(count($ids), $discussion->participants);
        static::assertSame(
            $participants->count(),
            $discussion->participants->count()
        );

        $deleted = $discussion->removeParticipants($ids, false);

        static::assertSame(count($ids), $deleted);
        static::assertCount(
            $participants->count(),
            $discussion->participants
        );
    }

    /** @test */
    public function it_can_get_trashed_participants()
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $ids          = [1, 2, 3];
        $discussion   = $this->factory->create(Discussion::class);
        $participants = $discussion->addParticipants($ids);

        static::assertCount(count($ids), $discussion->participants);
        static::assertSame(
            $participants->count(),
            $discussion->participants->count()
        );

        $deleted = $discussion->removeParticipants($ids, false);

        static::assertSame(count($ids), $deleted);
        static::assertCount($participants->count(), $discussion->participants);

        $trashed = $discussion->getTrashedParticipants();

        static::assertCount($deleted, $trashed);
    }

    /** @test */
    public function it_can_restore_all_trashed_participants()
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $ids          = [1, 2, 3];
        $discussion   = $this->factory->create(Discussion::class);
        $discussion->addParticipants($ids);

        static::assertCount(count($ids), $discussion->participants);

        $deleted = $discussion->removeParticipants($ids);
        $trashed = $discussion->getTrashedParticipants();

        static::assertCount($deleted, $trashed);
        static::assertTrue($discussion->participants->isEmpty());

        $restored = $discussion->restoreAllParticipants();

        static::assertSame($deleted, $restored);
        static::assertFalse($discussion->participants->isEmpty());
    }

    /** @test */
    public function it_can_see_if_discussion_is_unread_by_user()
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $userId     = 1;
        $discussion = $this->factory->create(Discussion::class, ['updated_at' => Carbon::yesterday()]);
        $discussion->participants()->save(
            $this->factory->make(Participant::class, ['user_id' => $userId, 'last_read' => Carbon::now()])
        );

        static::assertFalse($discussion->isUnread($userId));

        $discussion = $this->factory->create(Discussion::class, ['subject' => 'Second Thread', 'updated_at' => Carbon::now()]);
        $discussion->participants()->save(
            $this->factory->make(Participant::class, ['user_id' => $userId, 'last_read' => Carbon::yesterday()])
        );

        static::assertTrue($discussion->isUnread($userId));
    }

    /** @test */
    public function it_can_check_if_has_users_and_participants()
    {
        $ids = [1, 2, 3];
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion   = $this->factory->create(Discussion::class);
        $participants = array_map(function ($userId) {
            return $this->factory->make(Participant::class, ['user_id' => $userId]);
        }, $ids);
        $discussion->participants()->saveMany($participants);

        foreach ($ids as $id) {
            static::assertTrue($discussion->hasParticipant($id));
        }

        static::assertFalse($discussion->hasParticipant(99));
    }

    /** @test */
    public function it_can_get_a_participant_by_user_id()
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $userId      = 1;
        $discussion  = $this->factory->create(Discussion::class);
        $discussion->participants()->save(
            $this->factory->make(Participant::class, ['user_id' => $userId])
        );

        $participant = $discussion->getParticipantByUserId($userId);

        static::assertInstanceOf(Participant::class, $participant);
    }

    /** @test */
    public function it_can_get_participants_string()
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $ids          = [1, 2, 3];
        $discussion   = $this->factory->create(Discussion::class);
        $participants = $discussion->addParticipants($ids);

        $rendered = $discussion->participantsString();
        static::assertContains(', ', $rendered);
        static::assertCount($participants->count(), explode(', ', $rendered));

        $rendered = $discussion->participantsString(2);

        static::assertContains(', ', $rendered);
        static::assertCount($participants->count() - 1, explode(', ', $rendered));
    }

    /** @test */
    public function it_can_get_participants_custom_string()
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $ids          = [1, 2, 3];
        $discussion   = $this->factory->create(Discussion::class);
        $participants = $discussion->addParticipants($ids);
        $callback     = function (Participant $participant) {
            return '#' . $participant->user_id;
        };

        $rendered = $discussion->participantsString(null, $callback);

        static::assertContains(', ', $rendered);
        static::assertCount($participants->count(), explode(', ', $rendered));
        foreach (explode(', ', $rendered) as $info) {
            static::assertStringStartsWith('#', $info);
        }

        $rendered = $discussion->participantsString(2, $callback);

        static::assertContains(', ', $rendered);
        static::assertCount($participants->count() - 1, explode(', ', $rendered));
        foreach (explode(', ', $rendered) as $info) {
            static::assertStringStartsWith('#', $info);
        }
    }

    /** @test */
    public function it_can_mark_last_read()
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion  = $this->factory->create(Discussion::class);
        $participant = $discussion->addParticipant(1);

        static::assertCount(1, $discussion->participants);
        static::assertEquals(
            $discussion->participants->first()->id,
            $participant->id
        );

        /** @var \Arcanedev\LaravelMessenger\Models\Participant  $participant */
        foreach ($discussion->participants as $participant) {
            static::assertNull($participant->last_read);
            $discussion->markAsRead($participant->user_id);
        }

        $discussion->load(['participants']);

        /** @var \Arcanedev\LaravelMessenger\Models\Participant  $participant */
        foreach ($discussion->participants as $participant) {
            static::assertNotNull($participant->last_read);
        }
    }

    /** @test */
    public function it_can_skip_mark_last_read_if_participant_not_found()
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion  = $this->factory->create(Discussion::class);

        static::assertFalse($discussion->markAsRead(10));
    }

    /** @test */
    public function it_can_check_participant_if_has_not_marked_as_read()
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion  = $this->factory->create(Discussion::class);
        $participant = $discussion->addParticipant(1);

        static::assertCount(1, $discussion->participants);
        static::assertTrue($discussion->isUnread($participant->user_id));

        $discussion->markAsRead($participant->user_id);

        static::assertFalse($discussion->isUnread($participant->user_id));
    }

    /** @test */
    public function it_can_get_the_latest_message()
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
    public function it_get_all_latest_discussions()
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
    public function it_can_get_discussions_by_subject()
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
    public function it_can_get_discussions_by_strict_subject()
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
    public function it_can_get_users_ids()
    {
        /** * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $ids          = [1, 2, 3];
        $discussion   = $this->factory->create(Discussion::class);
        $discussion->addParticipants($ids);

        $usersIds = $discussion->participantsUserIds();

        static::assertInstanceOf(Collection::class, $usersIds);
        static::assertCount(count($ids), $usersIds);
        static::assertEquals($ids, $usersIds->toArray());

        // Ignore the user id if exists
        $usersIds = $discussion->participantsUserIds(3);

        static::assertInstanceOf(Collection::class, $usersIds);
        static::assertCount(count($ids), $usersIds);
        static::assertEquals($ids, $usersIds->toArray());

        $ids[]    = 4;
        $usersIds = $discussion->participantsUserIds(4);

        static::assertInstanceOf(Collection::class, $usersIds);
        static::assertCount(count($ids), $usersIds);
        static::assertEquals($ids, $usersIds->toArray());
    }

    /** @test */
    public function it_can_get_all_discussions_between_specific_users()
    {
        /**
         * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussionOne
         * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussionTwo
         */
        $userIdOne     = 1;
        $userIdTwo     = 2;
        $discussionOne = $this->factory->create(Discussion::class);

        $discussionOne->participants()->saveMany([
            $this->factory->make(Participant::class, ['user_id' => $userIdOne]),
            $this->factory->make(Participant::class, ['user_id' => $userIdTwo]),
        ]);

        $discussionTwo = $this->factory->create(Discussion::class);
        $discussionTwo->participants()->save(
            $this->factory->make(Participant::class, ['user_id' => $userIdOne])
        );

        $discussions = Discussion::between([$userIdOne, $userIdTwo])->get();

        static::assertCount(1, $discussions);
    }

    /** @test */
    public function it_can_get_all_discussions_for_a_user()
    {
        /**
         * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussionOne
         * @var \Arcanedev\LaravelMessenger\Models\Discussion  $discussionTwo
         */
        $userId         = 1;
        $discussionOne  = $this->factory->create(Discussion::class);
        $discussionOne->participants()->save(
            $this->factory->make(Participant::class, ['user_id' => $userId])
        );

        $discussionTwo  = $this->factory->create(Discussion::class, ['subject' => 'Second Thread']);
        $discussionTwo->participants()->save(
            $this->factory->make(Participant::class, ['user_id' => $userId])
        );

        $discussions = Discussion::forUser($userId)->with(['participants'])->get();

        static::assertCount(2, $discussions);
    }

    /** @test */
    public function it_can_get_all_users_for_a_discussion()
    {
        /** @var  \Arcanedev\LaravelMessenger\Models\Discussion  $discussion */
        $discussion = $this->factory->create(Discussion::class);

        $discussion->participants()->saveMany([
            $this->factory->make(Participant::class, ['user_id' => 1]),
            $this->factory->make(Participant::class, ['user_id' => 2]),
        ]);

        $discussionUsers = $discussion->users()->get();

        static::assertCount(2, $discussionUsers);
        static::assertSame([1, 2], $discussionUsers->pluck('id')->toArray());
    }

    /** @test */
    public function it_can_get_all_discussions_for_a_user_with_new_messages()
    {
        $userId = 1;
        $discussionOne = $this->factory->create(Discussion::class, ['updated_at' => Carbon::yesterday()]);
        $discussionOne->participants()->save(
            $this->factory->make(Participant::class, ['user_id' => $userId, 'last_read' => Carbon::now()])
        );

        $discussionTwo = $this->factory->create(Discussion::class, ['subject' => 'Second Thread', 'updated_at' => Carbon::now()]);
        $discussionTwo->participants()->save(
            $this->factory->make(Participant::class, ['user_id' => $userId, 'last_read' => Carbon::yesterday()])
        );

        $discussions = Discussion::forUserWithNewMessages($userId)->get();

        static::assertCount(1, $discussions);
    }

    /** @test */
    public function it_should_get_all_unread_messages_for_user()
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Discussion $discussion */
        $discussion = $this->factory->create(Discussion::class);
        $discussion->participants()->saveMany([
            $participantOne = $this->factory->make(Participant::class, ['user_id' => 1]),
            $participantTwo = $this->factory->make(Participant::class, ['user_id' => 2]),
        ]);
        $discussion->messages()->save(
            $this->factory->make(Message::class, ['body' => 'Message 1', 'created_at' => Carbon::now()])
        );
        $discussion->markAsRead($participantTwo->user_id);
        // Simulate delay after last read
        sleep(1);

        $discussion->messages()->save(
            $this->factory->make(Message::class, ['body' => 'Message 2', 'created_at' => Carbon::now()])
        );

        $messages = $discussion->userUnreadMessages(1);

        static::assertInstanceOf(Collection::class, $messages);
        static::assertCount(2, $messages);
        static::assertEquals('Message 1', $messages->first()->body);

        $messages = $discussion->userUnreadMessages(2);

        static::assertInstanceOf(Collection::class, $messages);
        static::assertCount(1, $messages);
        static::assertEquals('Message 2', $messages->first()->body);
    }

    /** @test */
    public function it_can_get_count_of_all_unread_messages_for_user()
    {
        /**
         * @var \Arcanedev\LaravelMessenger\Models\Discussion   $discussion
         * @var \Arcanedev\LaravelMessenger\Models\Participant  $participantOne
         * @var \Arcanedev\LaravelMessenger\Models\Participant  $participantTwo
         */
        $discussion = $this->factory->create(Discussion::class);
        $discussion->participants()->saveMany([
            $participantOne = $this->factory->make(Participant::class),
            $participantTwo = $this->factory->make(Participant::class, ['user_id' => 2]),
        ]);
        $discussion->messages()->save(
            $this->factory->make(Message::class, ['body' => 'Message 1', 'created_at' => Carbon::now()])
        );
        $discussion->markAsRead($participantTwo->user_id);
        // Simulate delay after last read
        sleep(1);

        $discussion->messages()->save(
            $this->factory->make(Message::class, ['body' => 'Message 2', 'created_at' => Carbon::now()])
        );

        static::assertCount(2, $discussion->userUnreadMessages($participantOne->user_id));
        static::assertCount(1, $discussion->userUnreadMessages($participantTwo->user_id));

        // it must return empty unread messages collection with invalid participant id
        $messages = $discussion->userUnreadMessages(0);

        static::assertInstanceOf(Collection::class, $messages);
        static::assertCount(0, $messages);
    }
}
