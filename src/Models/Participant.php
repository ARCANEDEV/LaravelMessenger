<?php namespace Arcanedev\LaravelMessenger\Models;

use Arcanedev\LaravelMessenger\Bases\Model;
use Arcanedev\LaravelMessenger\Contracts\Participant as ParticipantContract;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class     Participant
 *
 * @package  Arcanedev\LaravelMessenger\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int                                            id
 * @property  int                                            discussion_id
 * @property  \Arcanedev\LaravelMessenger\Models\Discussion  discussion
 * @property  int                                            user_id
 * @property  \Illuminate\Database\Eloquent\Model            user
 * @property  \Carbon\Carbon                                 last_read
 * @property  \Carbon\Carbon                                 created_at
 * @property  \Carbon\Carbon                                 updated_at
 * @property  \Carbon\Carbon                                 deleted_at
 */
class Participant extends Model implements ParticipantContract
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
    protected $fillable = ['discussion_id', 'user_id', 'last_read'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['last_read', 'deleted_at'];

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
            $this->getTableFromConfig('participants', 'participants')
        );

        parent::__construct($attributes);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Relationships
     | ------------------------------------------------------------------------------------------------
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
     * User relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(
            $this->getModelFromConfig('users')
        );
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get the participant string info.
     *
     * @return string
     */
    public function stringInfo()
    {
        return $this->user->name;
    }
}
