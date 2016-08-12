<?php namespace Arcanedev\LaravelMessenger\Models;

use Arcanedev\LaravelMessenger\Bases\Model;
use Arcanedev\LaravelMessenger\Contracts\Discussion as DiscussionContract;
use Arcanedev\LaravelMessenger\Contracts\Message as MessageContract;
use Arcanedev\LaravelMessenger\Contracts\Participant as ParticipantContract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class     Discussion
 *
 * @package  Arcanedev\LaravelMessenger\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
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
class Discussion extends Model implements DiscussionContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use SoftDeletes;

    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['subject'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable(
            $this->getTableFromConfig('discussions', 'discussions')
        );

        parent::__construct($attributes);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Relationships
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Participants relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany(
            $this->getModelFromConfig('participants', Participant::class)
        );
    }

    /**
     * Messages relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(
            $this->getModelFromConfig('messages', Participant::class)
        );
    }

    /**
     * Get the user that created the first message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->messages()->oldest()->first()->user();
    }

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
    public function scopeForUser(Builder $query, $userId)
    {
        $participants = $this->getParticipantsTable();

        return $query->join($participants, function ($join) use ($participants, $userId) {
            /** @var \Illuminate\Database\Query\JoinClause $join */
            $join->on($this->getQualifiedKeyName(), '=', "{$participants}.discussion_id")
                 ->where("{$participants}.user_id", '=', $userId)
                 ->whereNull("{$participants}.deleted_at");
        });
    }

    /**
     * Scope discussions with new messages that the user is associated with.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int                                    $userId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUserWithNewMessages(Builder $query, $userId)
    {
        $participants = $this->getParticipantsTable();
        $discussions  = $this->getTable();
        $prefix       = $this->getConnection()->getTablePrefix();

        return $this->scopeForUser($query, $userId)
            ->where(function (Builder $query) use ($participants, $discussions, $prefix) {
                $expression = $this->getConnection()->raw("{$prefix}{$participants}.last_read");

                $query->where("{$discussions}.updated_at", '>', $expression)
                      ->orWhereNull("{$participants}.last_read");
            });
    }

    /**
     * Scope discussions between given user ids.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array                                  $userIds
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetween(Builder $query, array $userIds)
    {
        $participants = $this->getParticipantsTable();

        return $query->whereHas($participants, function (Builder $query) use ($userIds) {
            $query->whereIn('user_id', $userIds)
                ->groupBy('discussion_id')
                ->havingRaw('COUNT(discussion_id)=' . count($userIds));
        });
    }

    /**
     * Get the participants table name.
     *
     * @return string
     */
    protected function getParticipantsTable()
    {
        return $this->getTableFromConfig('participants', 'participants');
    }

    /**
     * Scope the query by the subject.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string                                 $subject
     * @param  bool                                   $strict
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSubject(Builder $query, $subject, $strict = false)
    {
        $subject = $strict ? $subject : "%{$subject}%";

        return $query->where('subject', 'like', $subject);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the latest_message attribute.
     *
     * @return \Arcanedev\LaravelMessenger\Models\Message
     */
    public function getLatestMessageAttribute()
    {
        return $this->messages->sortByDesc('created_at')->first();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Returns all of the latest discussions by `updated_at` date.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getLatest()
    {
        return self::latest('updated_at')->get();
    }

    /**
     * Returns all discussions by subject.
     *
     * @param  string  $subject
     * @param  bool    $strict
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getBySubject($subject, $strict = false)
    {
        return self::subject($subject, $strict)->get();
    }

    /**
     * Returns an array of user ids that are associated with the discussion.
     *
     * @param  int|null  $userId
     *
     * @return array
     */
    public function participantsUserIds($userId = null)
    {
        $usersIds = $this->participants()
            ->withTrashed()
            ->pluck('user_id')
            ->toArray();

        if ($userId && ! in_array($userId, $usersIds)) {
            $usersIds[] = $userId;
        }

        return $usersIds;
    }

    /**
     * Add a user to discussion as a participant.
     *
     * @param  int   $userId
     *
     * @return \Arcanedev\LaravelMessenger\Models\Participant
     */
    public function addParticipant($userId)
    {
        /** @var \Arcanedev\LaravelMessenger\Models\Participant $participant */
        $participant = $this->participants()->firstOrCreate([
            'user_id'       => $userId,
            'discussion_id' => $this->id,
        ]);

        return $participant;
    }

    /**
     * Add users to discussion as participants.
     *
     * @param  array  $userIds
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function addParticipants(array $userIds)
    {
        foreach ($userIds as $userId) {
            $this->addParticipant($userId);
        }

        return $this->participants;
    }

    /**
     * Remove a participant from discussion.
     *
     * @param  int   $userId
     * @param  bool  $reload
     *
     * @return int
     */
    public function removeParticipant($userId, $reload = true)
    {
        $deleted = $this->participants()
            ->where('discussion_id', $this->id)
            ->where('user_id', $userId)
            ->delete();

        if ($reload) $this->load(['participants']);

        return $deleted;
    }

    /**
     * Remove participants from discussion.
     *
     * @param  array  $userIds
     * @param  bool   $reload
     *
     * @return int
     */
    public function removeParticipants(array $userIds, $reload = true)
    {
        $deleted = $this->participants()
            ->whereIn('user_id', $userIds)
            ->where('discussion_id', $this->id)
            ->delete();

        if ($reload) $this->load(['participants']);

        return $deleted;
    }

    /**
     * Mark a discussion as read for a user.
     *
     * @param  int  $userId
     *
     * @return bool|int
     */
    public function markAsRead($userId)
    {
        if ($participant = $this->getParticipantByUserId($userId)) {
            return $participant->update([
                'last_read' => Carbon::now()
            ]);
        }

        return false;
    }

    /**
     * See if the current thread is unread by the user.
     *
     * @param  int  $userId
     *
     * @return bool
     */
    public function isUnread($userId)
    {
        return ($participant = $this->getParticipantByUserId($userId))
            ? $participant->last_read < $this->updated_at
            : false;
    }

    /**
     * Finds the participant record from a user id.
     *
     * @param  int  $userId
     *
     * @return \Arcanedev\LaravelMessenger\Models\Participant
     */
    public function getParticipantByUserId($userId)
    {
        return $this->participants()
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Get the trashed participants.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTrashedParticipants()
    {
        return $this->participants()
            ->onlyTrashed()
            ->get();
    }

    /**
     * Restores all participants within a discussion.
     *
     * @param  bool  $reload
     *
     * @return int
     */
    public function restoreAllParticipants($reload = true)
    {
        $participants = $this->getTrashedParticipants();
        $restored     = $participants->filter(function (ParticipantContract $participant) {
            return $participant->restore();
        })->count();

        if ($reload) $this->load(['participants']);

        return $restored;
    }

    /**
     * Generates a participant information as a string.
     *
     * @param  int|null       $ignoredUserId
     * @param  \Closure|null  $callback
     * @param  string         $glue
     *
     * @return string
     */
    public function participantsString($ignoredUserId = null, $callback = null, $glue = ', ')
    {
        /** @var \Illuminate\Database\Eloquent\Collection $participants */
        $participants = $this->participants->load(['user']);

        if (is_null($callback)) {
            // By default: the participant name
            $callback = function (ParticipantContract $participant) {
                return $participant->stringInfo();
            };
        }

        return $participants->filter(function (ParticipantContract $participant) use ($ignoredUserId) {
            return $participant->user_id !== $ignoredUserId;
        })->map($callback)->implode($glue);
    }

    /**
     * Checks to see if a user is a current participant of the discussion.
     *
     * @param  int  $userId
     *
     * @return bool
     */
    public function hasParticipant($userId)
    {
        return $this->participants()
            ->where('user_id', '=', $userId)
            ->count() > 0;
    }

    /**
     * Returns array of unread messages in discussion for given user.
     *
     * @param  int  $userId
     *
     * @return \Illuminate\Support\Collection
     */
    public function userUnreadMessages($userId)
    {
        /** @var \Illuminate\Database\Eloquent\Collection $messages */
        $participant = $this->getParticipantByUserId($userId);

        if (is_null($participant))            return collect();
        if (is_null($participant->last_read)) return $this->messages;

        return $this->messages->filter(function (MessageContract $message) use ($participant) {
            return $message->updated_at->gt($participant->last_read);
        });
    }

    /**
     * Returns count of unread messages in thread for given user.
     *
     * @param  int  $userId
     *
     * @return int
     */
    public function userUnreadMessagesCount($userId)
    {
        return $this->userUnreadMessages($userId)->count();
    }
}
