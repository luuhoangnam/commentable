<?php

namespace Namest\Commentable;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Trait CommentableTrait
 *
 * @author  Nam Hoang Luu <nam@mbearvn.com>
 * @package Namest\Commentable
 *
 * @property-read Collection $commenters
 *
 * @method static QueryBuilder|EloquentBuilder|$this hasCommentBy(Model $commenter)
 */
trait CommentableTrait
{
    /**
     * @param EloquentBuilder|QueryBuilder $query
     * @param Model                        $commenter
     *
     * @return QueryBuilder
     */
    public function scopeHasCommentBy($query, Model $commenter)
    {
        $table   = $this->getTable();
        $builder = $query->getQuery();

        $builder->join('comments', 'comments.commentable_id', '=', "{$table}.id")
                ->where('comments.commentable_type', '=', get_class($this))
                ->where('comments.commenter_type', '=', get_class($commenter))
                ->where('comments.commenter_id', '=', $commenter->getKey());

        return $builder;
    }

    /**
     * TODO Optimize performance by reduce SQL query
     *
     * @return array
     */
    public function getCommentersAttribute()
    {
        $relation = $this->hasMany(Comment::class, 'commentable_id');
        $relation->getQuery()->where('commentable_type', '=', get_class($this));

        return new Collection(array_map(function ($like) {
            return forward_static_call([$like['commenter_type'], 'find'], $like['commenter_id']);
        }, $relation->getResults()->toArray()));
    }
}
