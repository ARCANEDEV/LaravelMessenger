<?php namespace Arcanedev\LaravelMessenger\Models;

use Arcanedev\LaravelMessenger\Contracts\Message as MessageContract;

/**
 * Class     Message
 *
 * @package  Arcanedev\LaravelMessenger\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int                                            id
 * @property  int                                            discussion_id
 * @property  string                                         participable_type
 * @property  int                                            participable_id
 * @property  int                                            body
 * @property  \Illuminate\Support\Carbon                     created_at
 * @property  \Illuminate\Support\Carbon                     updated_at
 * @property  \Illuminate\Support\Carbon                     deleted_at
 *
 * @property  \Arcanedev\LaravelMessenger\Models\Discussion  discussion
 * @property  \Illuminate\Database\Eloquent\Model            participable
 * @property  \Illuminate\Database\Eloquent\Model            author
 * @property  \Illuminate\Database\Eloquent\Collection       participations
 * @property  \Illuminate\Database\Eloquent\Collection       recipients
 */
class Message extends Model implements MessageContract
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The relationships that should be touched on save.
     *
     * @var array
     */
    protected $touches = ['discussion'];

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = [
        'discussion_id',
        'body',
    ];

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
        'id'              => 'integer',
        'discussion_id'   => 'integer',
        'participable_id' => 'integer',
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
            config('messenger.messages.table', 'messages')
        );

        parent::__construct($attributes);
    }

    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */

    /**
     * Discussion relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function discussion()
    {
        return $this->belongsTo(
            config('messenger.discussions.model', Discussion::class)
        );
    }

    /**
     * User/Author relationship (alias).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function author()
    {
        return $this->participable();
    }

    /**
     * Participable relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function participable()
    {
        return $this->morphTo();
    }

    /**
     * Participations relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participations()
    {
        return $this->hasMany(
            config('messenger.participations.model', Participation::class),
            'discussion_id',
            'discussion_id'
        );
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Recipients of this message.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecipientsAttribute()
    {
        $morph = config('messenger.users.morph', 'participable');

        return $this->participations->reject(function (Participation $participant) use ($morph) {
            return $participant->getAttribute("{$morph}_id") === $this->getAttribute("{$morph}_id")
                && $participant->getAttribute("{$morph}_type") === $this->getAttribute("{$morph}_type");
        });
    }
}
