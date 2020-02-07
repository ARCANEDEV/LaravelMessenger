<?php

declare(strict_types=1);

namespace Arcanedev\LaravelMessenger\Models;

use Arcanedev\LaravelMessenger\Contracts\Discussion as DiscussionContract;
use Arcanedev\LaravelMessenger\Contracts\Message as MessageContract;
use Arcanedev\LaravelMessenger\Contracts\Participation as ParticipationContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class     Discussion
 *
 * @package  Arcanedev\LaravelMessenger\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int                         id
 * @property  string                      subject
 * @property  \Illuminate\Support\Carbon  created_at
 * @property  \Illuminate\Support\Carbon  updated_at
 * @property  \Illuminate\Support\Carbon  deleted_at
 *
 * @property  \Illuminate\Database\Eloquent\Model         creator
 * @property  \Illuminate\Database\Eloquent\Collection    messages
 * @property  \Illuminate\Database\Eloquent\Collection    participations
 * @property  \Arcanedev\LaravelMessenger\Models\Message  latest_message
 *
 * @method static  \Illuminate\Database\Eloquent\Builder|static  subject(string $subject, bool $strict)
 * @method static  \Illuminate\Database\Eloquent\Builder|static  between(\Illuminate\Support\Collection|array $participables)
 * @method static  \Illuminate\Database\Eloquent\Builder|static  forUser(\Illuminate\Database\Eloquent\Model|mixed $participable)
 * @method static  \Illuminate\Database\Eloquent\Builder|static  forUserWithNewMessages(\Illuminate\Database\Eloquent\Model|mixed $participable)
 */
class Discussion extends Model implements DiscussionContract
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable(
            config('messenger.discussions.table', 'discussions')
        );

        parent::__construct($attributes);
    }

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Participants relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participations()
    {
        return $this->hasMany(
            config('messenger.participations.model', Participation::class)
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
            config('messenger.messages.model', Message::class)
        );
    }

    /**
     * Get the participable that created the first message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->messages()->oldest()->first()->author();
    }

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
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeForUser(Builder $query, EloquentModel $participable)
    {
        $table = $this->getParticipationsTable();
        $morph = config('messenger.users.morph', 'participable');

        return $query
            ->join($table, $this->getQualifiedKeyName(), '=', "{$table}.discussion_id")
            ->where("{$table}.{$morph}_type", '=', $participable->getMorphClass())
            ->where("{$table}.{$morph}_id", '=', $participable->getKey())
            ->whereNull("{$table}.deleted_at")
            ->select("{$this->getTable()}.*");
    }

    /**
     * Scope discussions with new messages that the participable is associated with.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model    $participable
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeForUserWithNewMessages(Builder $query, EloquentModel $participable)
    {
        $prefix         = $this->getConnection()->getTablePrefix();
        $participations = $this->getParticipationsTable();
        $discussions    = $this->getTable();

        return $this->scopeForUser($query, $participable)
                    ->where(function (Builder $query) use ($participations, $discussions, $prefix) {
                        $expression = $this->getConnection()->raw("{$prefix}{$participations}.last_read");

                        $query->where("{$discussions}.updated_at", '>', $expression)
                              ->orWhereNull("{$participations}.last_read");
                    });
    }

    /**
     * Scope discussions between given participables.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Support\Collection|array   $participables
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeBetween(Builder $query, $participables)
    {
        return $query->whereHas($this->getParticipationsTable(), function (Builder $query) use ($participables) {
            $morph = config('messenger.users.morph', 'participable');
            $index = 0;

            foreach ($participables as $participable) {
                /** @var  \Illuminate\Database\Eloquent\Model  $participable */
                $clause = [
                    ["{$morph}_type", '=', $participable->getMorphClass()],
                    ["{$morph}_id", '=', $participable->getKey()],
                ];

                $query->where($clause, null, null, $index === 0 ? 'and' : 'or');

                $index++;
            }

            $query->groupBy('discussion_id')
                  ->havingRaw('COUNT(discussion_id)='.count($participables));
        });
    }

    /**
     * Scope the query by the subject.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string                                 $subject
     * @param  bool                                   $strict
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeSubject(Builder $query, $subject, $strict = false)
    {
        return $query->where('subject', 'like', $strict ? $subject : "%{$subject}%");
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
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

    /**
     * Get the participations table name.
     *
     * @return string
     */
    protected function getParticipationsTable()
    {
        return config('messenger.participations.table', 'participations');
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Returns all of the latest discussions by `updated_at` date.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getLatest()
    {
        return self::query()->latest('updated_at')->get();
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
     * Returns an array of participables that are associated with the discussion.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getParticipables()
    {
        return $this->participations()
            ->withTrashed()
            ->get()
            ->transform(function (ParticipationContract $participant) {
                return $participant->participable;
            })
            ->unique(function (EloquentModel $participable) {
                return $participable->getMorphClass().'-'.$participable->getKey();
            });
    }

    /**
     * Add a participable to discussion.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $participable
     *
     * @return \Arcanedev\LaravelMessenger\Models\Participation|mixed
     */
    public function addParticipant(EloquentModel $participable)
    {
        $morph = config('messenger.users.morph', 'participable');

        return $this->participations()->firstOrCreate([
            "{$morph}_id"   => $participable->getKey(),
            "{$morph}_type" => $participable->getMorphClass(),
            'discussion_id' => $this->id,
        ]);
    }

    /**
     * Add many participables to discussion.
     *
     * @param  \Illuminate\Support\Collection|array  $participables
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function addParticipants($participables)
    {
        foreach ($participables as $participable) {
            $this->addParticipant($participable);
        }

        return $this->participations;
    }

    /**
     * Remove a participable from discussion.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $participable
     * @param  bool                                 $reload
     *
     * @return int
     */
    public function removeParticipant(EloquentModel $participable, $reload = true)
    {
        return $this->removeParticipants([$participable], $reload);
    }

    /**
     * Remove many participables from discussion.
     *
     * @param  \Illuminate\Support\Collection|array  $participables
     * @param  bool   $reload
     *
     * @return int
     */
    public function removeParticipants($participables, $reload = true)
    {
        $morph   = config('messenger.users.morph', 'participable');
        $deleted = 0;

        foreach ($participables as $participable) {
            /** @var  \Illuminate\Database\Eloquent\Model  $participable */
            $deleted += $this->participations()
                ->where("{$morph}_type", '=', $participable->getMorphClass())
                ->where("{$morph}_id", '=', $participable->getKey())
                ->where('discussion_id', '=', $this->id)
                ->delete();
        }

        if ($reload)
            $this->load(['participations']);

        return $deleted;
    }

    /**
     * Mark a discussion as read for a participable.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $participable
     *
     * @return bool|int
     */
    public function markAsRead(EloquentModel $participable)
    {
        if ($participant = $this->getParticipationByParticipable($participable)) {
            return $participant->update(['last_read' => Carbon::now()]);
        }

        return false;
    }

    /**
     * See if the current thread is unread by the participable.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $participable
     *
     * @return bool
     */
    public function isUnread(EloquentModel $participable)
    {
        return ($participant = $this->getParticipationByParticipable($participable))
            ? $participant->last_read < $this->updated_at
            : false;
    }

    /**
     * Finds the participant record from a participable model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $participable
     *
     * @return \Arcanedev\LaravelMessenger\Models\Participation|mixed
     */
    public function getParticipationByParticipable(EloquentModel $participable)
    {
        $morph = config('messenger.users.morph', 'participable');

        return $this->participations()
            ->where("{$morph}_type", '=', $participable->getMorphClass())
            ->where("{$morph}_id", '=', $participable->getKey())
            ->first();
    }

    /**
     * Get the trashed participations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTrashedParticipations()
    {
        return $this->participations()->onlyTrashed()->get();
    }

    /**
     * Restores all participations within a discussion.
     *
     * @param  bool  $reload
     *
     * @return int
     */
    public function restoreAllParticipations($reload = true)
    {
        $restored = $this->getTrashedParticipations()
            ->filter(function (ParticipationContract $participant) {
                return $participant->restore();
            })
            ->count();

        if ($reload)
            $this->load(['participations']);

        return $restored;
    }

    /**
     * Generates a participant information as a string.
     *
     * @param  \Closure|null  $callback
     * @param  string         $glue
     *
     * @return string
     */
    public function participationsString($callback = null, $glue = ', ')
    {
        /** @var \Illuminate\Database\Eloquent\Collection $participations */
        $participations = $this->participations->load(['participable']);

        if (is_null($callback)) {
            // By default: the participant name
            $callback = function (ParticipationContract $participant) {
                return $participant->stringInfo();
            };
        }

        return $participations->map($callback)->implode($glue);
    }

    /**
     * Checks to see if a participable is a current participant of the discussion.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $participable
     *
     * @return bool
     */
    public function hasParticipation(EloquentModel $participable)
    {
        $morph = config('messenger.users.morph', 'participable');

        return $this->participations()
            ->where("{$morph}_id", '=', $participable->getKey())
            ->where("{$morph}_type", '=', $participable->getMorphClass())
            ->count() > 0;
    }

    /**
     * Get the unread messages in discussion for a specific participable.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $participable
     *
     * @return \Illuminate\Support\Collection
     */
    public function getUnreadMessages(EloquentModel $participable)
    {
        $participation = $this->getParticipationByParticipable($participable);

        if (is_null($participation))
            return new Collection;

        return is_null($participation->last_read)
            ? $this->messages->toBase()
            : $this->messages->filter(function (MessageContract $message) use ($participation) {
                return $message->updated_at->gt($participation->last_read);
            })->toBase();
    }
}
