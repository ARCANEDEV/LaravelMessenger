<?php namespace Arcanedev\LaravelMessenger\Traits;

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
 * @property  \Illuminate\Database\Eloquent\Collection  participations
 * @property  \Illuminate\Database\Eloquent\Collection  messages
 */
trait Messagable
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use ConfigHelper;

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Thread relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function discussions()
    {
        return $this->morphToMany(
            $this->getModelFromConfig('discussions', Models\Discussion::class),
            'participable',
            $this->getTableFromConfig('participations', 'participations')
        );
    }

    /**
     * Participations relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participations()
    {
        return $this->morphMany(
            $this->getModelFromConfig('participations', Models\Participation::class),
            'participable'
        );
    }

    /**
     * Message relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->morphMany(
            $this->getModelFromConfig('messages', Models\Message::class),
            'participable'
        );
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
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
        $participationsTable = $this->getTableFromConfig('participations', 'participations');
        $discussionsTable    = $this->getTableFromConfig('discussions', 'discussions');

        return $this->discussions()->where(function (Builder $query) use ($participationsTable, $discussionsTable) {
            $query->whereNull("$participationsTable.last_read");
            $query->orWhere(
                "$discussionsTable.updated_at", '>', $this->getConnection()->raw("$participationsTable.last_read")
            );
        })->get();
    }

    /* -----------------------------------------------------------------
     |  Eloquent Methods
     | -----------------------------------------------------------------
     */

    /**
     * Define a many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string  $relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    abstract public function belongsToMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null,
                                  $parentKey = null, $relatedKey = null, $relation = null);

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
