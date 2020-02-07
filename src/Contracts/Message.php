<?php

declare(strict_types=1);

namespace Arcanedev\LaravelMessenger\Contracts;

/**
 * Interface  Message
 *
 * @package   Arcanedev\LaravelMessenger\Contracts
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int                                            id
 * @property  int                                            discussion_id
 * @property  \Arcanedev\LaravelMessenger\Models\Discussion  discussion
 * @property  int                                            user_id
 * @property  \Illuminate\Database\Eloquent\Model            user
 * @property  \Illuminate\Database\Eloquent\Model            author
 * @property  int                                            body
 * @property  \Carbon\Carbon                                 created_at
 * @property  \Carbon\Carbon                                 updated_at
 * @property  \Illuminate\Database\Eloquent\Collection       participations
 * @property  \Illuminate\Database\Eloquent\Collection       recipients
 */
interface Message
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
     * User/Author relationship (alias).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author();

    /**
     * Participable relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function participable();

    /**
     * Participations relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participations();

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Recipients of this message.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecipientsAttribute();
}
