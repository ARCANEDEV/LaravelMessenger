<?php namespace Arcanedev\LaravelMessenger\Contracts;

use Illuminate\Database\Eloquent\Builder;

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
 * @property  \Illuminate\Database\Eloquent\Collection    participants
 * @property  \Arcanedev\LaravelMessenger\Models\Message  latest_message
 *
 * @method static \Illuminate\Database\Eloquent\Builder  subject(string $subject, bool $strict)
 * @method static \Illuminate\Database\Eloquent\Builder  between(array $usersIds)
 * @method static \Illuminate\Database\Eloquent\Builder  forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder  forUserWithNewMessages(int $userId)
 */
interface Discussion
{
    /* ------------------------------------------------------------------------------------------------
     |  Relationships
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Participants relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants();

    /**
     * Messages relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages();

    /**
     * Get the user that created the first message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator();

    /* ------------------------------------------------------------------------------------------------
     |  Scopes
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Scope discussions that the user is associated with.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int                                    $userId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser(Builder $query, $userId);

    /**
     * Scope discussions with new messages that the user is associated with.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int                                    $userId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUserWithNewMessages(Builder $query, $userId);

    /**
     * Scope discussions between given user ids.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array                                  $userIds
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetween(Builder $query, array $userIds);

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

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the latest_message attribute.
     *
     * @return \Arcanedev\LaravelMessenger\Models\Message
     */
    public function getLatestMessageAttribute();

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
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
     * Returns an array of user ids that are associated with the discussion.
     *
     * @param  int|null  $userId
     *
     * @return array
     */
    public function participantsUserIds($userId = null);

    /**
     * Add a user to discussion as a participant.
     *
     * @param  int   $userId
     *
     * @return \Arcanedev\LaravelMessenger\Models\Participant
     */
    public function addParticipant($userId);

    /**
     * Add users to discussion as participants.
     *
     * @param  array  $userIds
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function addParticipants(array $userIds);

    /**
     * Remove a participant from discussion.
     *
     * @param  int   $userId
     * @param  bool  $reload
     *
     * @return int
     */
    public function removeParticipant($userId, $reload = true);

    /**
     * Remove participants from discussion.
     *
     * @param  array  $userIds
     * @param  bool   $reload
     *
     * @return int
     */
    public function removeParticipants(array $userIds, $reload = true);

    /**
     * Mark a discussion as read for a user.
     *
     * @param  int  $userId
     *
     * @return bool|int
     */
    public function markAsRead($userId);

    /**
     * See if the current thread is unread by the user.
     *
     * @param  int  $userId
     *
     * @return bool
     */
    public function isUnread($userId);

    /**
     * Finds the participant record from a user id.
     *
     * @param  int  $userId
     *
     * @return \Arcanedev\LaravelMessenger\Models\Participant
     */
    public function getParticipantByUserId($userId);

    /**
     * Get the trashed participants.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTrashedParticipants();

    /**
     * Restores all participants within a discussion.
     *
     * @param  bool  $reload
     *
     * @return int
     */
    public function restoreAllParticipants($reload = true);

    /**
     * Generates a participant information as a string.
     *
     * @param  int|null       $ignoredUserId
     * @param  \Closure|null  $callback
     * @param  string         $glue
     *
     * @return string
     */
    public function participantsString($ignoredUserId = null, $callback = null, $glue = ', ');

    /**
     * Checks to see if a user is a current participant of the discussion.
     *
     * @param  int  $userId
     *
     * @return bool
     */
    public function hasParticipant($userId);

    /**
     * Returns array of unread messages in discussion for given user.
     *
     * @param  int  $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function userUnreadMessages($userId);

    /**
     * Returns count of unread messages in thread for given user.
     *
     * @param  int  $userId
     *
     * @return int
     */
    public function userUnreadMessagesCount($userId);
}
