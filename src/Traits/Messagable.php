<?php namespace Arcanedev\LaravelMessenger\Traits;

use Arcanedev\LaravelMessenger\Contracts\Discussion as DiscussionContract;
use Arcanedev\LaravelMessenger\Contracts\Participant as ParticipantContract;
use Arcanedev\LaravelMessenger\Models;

/**
 * Trait     Messagable
 *
 * @package  Arcanedev\LaravelMessenger\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int                                       id
 * @property  \Illuminate\Database\Eloquent\Collection  discussions
 * @property  \Illuminate\Database\Eloquent\Collection  participants
 * @property  \Illuminate\Database\Eloquent\Collection  messages
 */
trait Messagable
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use ConfigHelper;

    /* ------------------------------------------------------------------------------------------------
     |  Relationships
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Thread relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function discussions()
    {
        return $this->belongsToMany(
            $this->getModelFromConfig('discussions', Models\Discussion::class),
            $this->getTableFromConfig('participants', 'participants'),
            'user_id',
            'discussion_id'
        );
    }

    /**
     * Participants relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany(
            $this->getModelFromConfig('participants', Models\Participant::class)
        );
    }

    /**
     * Message relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(
            $this->getModelFromConfig('messages', Models\Message::class)
        );
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Returns the new messages count for user.
     *
     * @return int
     */
    public function newMessagesCount()
    {
        return count($this->discussionsWithNewMessages());
    }

    /**
     * Returns all discussions IDs with new messages.
     *
     * @return array
     */
    public function discussionsWithNewMessages()
    {
        /** @var \Illuminate\Database\Eloquent\Collection  $participants */
        $participants = app(ParticipantContract::class)
            ->where('user_id', $this->id)
            ->get()
            ->pluck('last_read', 'discussion_id');

        if ($participants->isEmpty()) return [];

        /** @var \Illuminate\Database\Eloquent\Collection  $discussions */
        $discussions = app(DiscussionContract::class)
            ->whereIn('id', $participants->keys()->toArray())
            ->get();

        return $discussions->filter(function (Models\Discussion $discussion) use ($participants) {
            return $discussion->updated_at > $participants->get($discussion->id);
        })->pluck('id')->toArray();
    }
}
