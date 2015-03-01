<?php

namespace Namest\Commentable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Class Comment
 *
 * @property string     message
 * @property int        commenter_id
 * @property string     commenter_type
 * @property int        commentable_id
 * @property string     commentable_type
 *
 * @property-read Model $commenter
 * @property-read Model $commentable
 *
 * @method static QueryBuilder|EloquentBuilder|$this by(Model $commenter, $commentableType = null)
 *
 * @author  Nam Hoang Luu <nam@mbearvn.com>
 * @package Namest\Commentable
 *
 */
class Comment extends Model
{
    /**
     * @param Model $commentable
     *
     * @return Comment
     * @throws \Exception
     */
    public function about(Model $commentable)
    {
        // Check have setted message,commenter yet?
        if (is_null($this->commenter_id) || is_null($this->commenter_type))
            throw new \BadMethodCallException('Can not call `about` method directly.' .
                                              'You must use: $commenter->commment($message)->about($object);');

        // Event
        /** @var Model $this */
        $events = $this->getEventDispatcher();
        $events->fire('namest.commentable.commenting', [$commentable, $this->message]);

        // Set commentable
        $this->commentable_id   = $commentable->getKey();
        $this->commentable_type = get_class($commentable);

        // Save
        if ($this->save()) {
            $events->fire('namest.commentable.commented', [$this]);

            return $this;
        }

        throw new \Exception("Can not save comment.");
    }

    /**
     * @param EloquentBuilder|QueryBuilder $query
     * @param Model                        $commenter
     * @param string                       $commentableType
     *
     * @return QueryBuilder
     */
    public function scopeBy($query, Model $commenter, $commentableType = null)
    {
        $builder = $query->getQuery();

        $builder->where('comments.commenter_type', '=', get_class($commenter))
                ->where('comments.commenter_id', '=', $commenter->getKey());

        if ($commentableType != null)
            $builder->where('comments.commentable_type', '=', $commentableType);

        return $builder;
    }

    /**
     * @param string $message
     *
     * @return string
     * @throws CensorException
     */
    public function censor($message)
    {
        $break   = config('commentable.censor.break');
        $replace = config('commentable.censor.replace');
        $words   = config('commentable.censor.words');

        foreach ($words as $word) {
            $oldMessage = $message;

            $quote   = preg_quote($word, '/');
            $message = preg_replace("/" . $quote . "/i", $replace, $message); // Not case sensitive

            if ($oldMessage !== $message && $break)
                throw new CensorException($word, "Not allowed word [{$word}] occur.");
        }

        return $message;
    }

    /**
     * @param string $message
     *
     * @return string
     */
    public function getMessageAttribute($message)
    {
        try {
            $message = $this->attributes['message'];

            return $this->censor($message);
        } catch ( CensorException $e ) {
            return $message;
        }
    }

    /**
     * @param string $message
     */
    public function setMessageAttribute($message)
    {
        $this->attributes['message'] = $this->censor($message);
    }

    /**
     * @return Model
     */
    public function getCommenterAttribute()
    {
        return forward_static_call([$this->commenter_type, 'find'], $this->commenter_id);
    }

    /**
     * @return Model
     */
    public function getCommentableAttribute()
    {
        return forward_static_call([$this->commentable_type, 'find'], $this->commentable_id);
    }
}
