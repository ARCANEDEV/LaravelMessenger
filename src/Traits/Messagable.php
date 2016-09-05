<?php namespace Arcanedev\LaravelMessenger\Traits;

use Arcanedev\LaravelMessenger\Contracts\Discussion as DiscussionContract;
use Arcanedev\LaravelMessenger\Contracts\Participant as ParticipantContract;
use Arcanedev\LaravelMessenger\Models;
use Illuminate\Database\Eloquent\Builder;

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
        return $this->discussionsWithNewMessages()->count();
    }

    /**
     * Returns all discussions IDs with new messages.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function discussionsWithNewMessages()
    {
        $participantsTable = $this->getTableFromConfig('participants', 'participants');
        $discussionsTable  = $this->getTableFromConfig('discussions', 'discussions');

        return $this->discussions()->where(function (Builder $query) use ($participantsTable, $discussionsTable) {
            $query->whereNull("$participantsTable.last_read");
            $query->orWhere(
                "$discussionsTable.updated_at", '>', $this->getConnection()->raw("$participantsTable.last_read")
            );
        })->get();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Required Methods
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Define a many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $table
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @param  string  $relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    abstract public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null);

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    abstract public function hasMany($related, $foreignKey = null, $localKey = null);
}
