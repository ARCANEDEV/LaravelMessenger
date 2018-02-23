<?php namespace Arcanedev\LaravelMessenger\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface  Discussion
 *
 * @package   Arcanedev\LaravelMessenger\Contracts
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int             id
 * @property  string          subject
 * @property  \Carbon\Carbon  created_at
 * @property  \Carbon\Carbon  updated_at
 * @property  \Carbon\Carbon  deleted_at
 *
 * @property  \Illuminate\Database\Eloquent\Model         creator
 * @property  \Illuminate\Database\Eloquent\Collection    messages
 * @property  \Illuminate\Database\Eloquent\Collection    participations
 * @property  \Arcanedev\LaravelMessenger\Models\Message  latest_message
 *
 * @method static  \Illuminate\Database\Eloquent\Builder  subject(string $subject, bool $strict)
 * @method static  \Illuminate\Database\Eloquent\Builder  between(array $participablesIds)
 * @method static  \Illuminate\Database\Eloquent\Builder  forUser(int $participableId)
 * @method static  \Illuminate\Database\Eloquent\Builder  forUserWithNewMessages(int $participableId)
 */
interface Discussion
{
    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Participations relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participations();

    /**
     * Messages relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages();

    /**
     * Get the participable that created the first message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator();

    /* -----------------------------------------------------------------
     |  Scopes
     | -----------------------------------------------------------------
     */

    /**
     * Scope discussions that the participable is associated with.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model    $participable
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser(Builder $query, Model $participable);

    /**
     * Scope discussions with new messages that the participable is associated with.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model    $participable
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUserWithNewMessages(Builder $query, Model $participable);

    /**
     * Scope discussions between given participables.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Support\Collection|array   $participables
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetween(Builder $query, $participables);

    /**
     * Scope the query by the subject.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string                                 $subject
     * @param  bool                                   $strict
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSubject(Builder $query, $subject, $strict = false);

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the latest_message attribute.
     *
     * @return \Arcanedev\LaravelMessenger\Models\Message
     */
    public function getLatestMessageAttribute();

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Returns all of the latest discussions by `updated_at` date.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getLatest();

    /**
     * Returns all discussions by subject.
     *
     * @param  string  $subject
     * @param  bool    $strict
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getBySubject($subject, $strict = false);

    /**
     * Returns an array of participables that are associated with the discussion.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getParticipables();

    /**
     * Add a participable to discussion.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $participable
     *
     * @return \Arcanedev\LaravelMessenger\Models\Participation
     */
    public function addParticipant(Model $participable);

    /**
     * Add many participables to discussion.
     *
     * @param  \Illuminate\Support\Collection|array  $participables
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function addParticipants($participables);

    /**
     * Remove a participable from discussion.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $participable
     * @param  bool                                 $reload
     *
     * @return int
     */
    public function removeParticipant(Model $participable, $reload = true);

    /**
     * Remove many participables from discussion.
     *
     * @param  \Illuminate\Support\Collection|array  $participables
     * @param  bool   $reload
     *
     * @return int
     */
    public function removeParticipants($participables, $reload = true);

    /**
     * Mark a discussion as read for a participable.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $participable
     *
     * @return bool|int
     */
    public function markAsRead(Model $participable);

    /**
     * See if the current thread is unread by the participable.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $participable
     *
     * @return bool
     */
    public function isUnread(Model $participable);

    /**
     * Finds the participation record from a participable model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $participable
     *
     * @return \Arcanedev\LaravelMessenger\Models\Participation|mixed
     */
    public function getParticipationByParticipable(Model $participable);

    /**
     * Get the trashed participations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTrashedParticipations();

    /**
     * Restores all participations within a discussion.
     *
     * @param  bool  $reload
     *
     * @return int
     */
    public function restoreAllParticipations($reload = true);

    /**
     * Generates a participation information as a string.
     *
     * @param  \Closure|null  $callback
     * @param  string         $glue
     *
     * @return string
     */
    public function participationsString($callback = null, $glue = ', ');

    /**
     * Checks to see if a participable is a current participation of the discussion.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $participable
     *
     * @return bool
     */
    public function hasParticipation(Model $participable);

    /**
     * Get the unread messages in discussion for a specific participable.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $participable
     *
     * @return \Illuminate\Support\Collection
     */
    public function getUnreadMessages(Model $participable);
}
