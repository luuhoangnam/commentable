<?php

namespace Namest\Commentable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Class Comment
 *
 * @property string message
 * @property int    commenter_id
 * @property string commenter_type
 * @property int    commentable_id
 * @property string commentable_type
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

        // Set commentable
        $this->commentable_id   = $commentable->getKey();
        $this->commentable_type = get_class($commentable);

        // Save
        if ($this->save())
            return $this;

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
}
