<?php namespace Arcanedev\LaravelMessenger\Contracts;

/**
 * Interface  Participant
 *
 * @package   Arcanedev\LaravelMessenger\Contracts
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
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
interface Participant
{
    /* -----------------------------------------------------------------
     |  Relationships
     | -----------------------------------------------------------------
     */
    /**
     * Discussion relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function discussion();

    /**
     * User relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user();

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */
    /**
     * Get the participant string info.
     *
     * @return string
     */
    public function stringInfo();

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */
    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool|null
     */
    public function restore();
}
