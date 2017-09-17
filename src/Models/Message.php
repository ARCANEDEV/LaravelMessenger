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
 * @property  int                                            user_id
 * @property  int                                            body
 * @property  \Carbon\Carbon                                 created_at
 * @property  \Carbon\Carbon                                 updated_at
 *
 * @property  \Arcanedev\LaravelMessenger\Models\Discussion  discussion
 * @property  \Illuminate\Database\Eloquent\Model            user
 * @property  \Illuminate\Database\Eloquent\Model            author
 * @property  \Illuminate\Database\Eloquent\Collection       participants
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
    protected $fillable = ['discussion_id', 'user_id', 'body'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'integer',
        'discussion_id' => 'integer',
        'user_id'       => 'integer',
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
            $this->getTableFromConfig('messages', 'messages')
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
            $this->getModelFromConfig('discussions', Discussion::class)
        );
    }

    /**
     * User/Author relationship (alias).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->user();
    }

    /**
     * User/Author relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(
            $this->getModelFromConfig('users')
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
            $this->getModelFromConfig('participants', Participant::class),
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
        return $this->participants->reject(function (Participant $participant) {
            return $participant->user_id === $this->user_id;
        });
    }
}
