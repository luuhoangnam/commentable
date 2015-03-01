<?php

namespace Namest\Commentable;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Trait CommenterTrait
 *
 * @author  Nam Hoang Luu <nam@mbearvn.com>
 * @package Namest\Commentable
 *
 * @property-read Collection $comments
 * @property-read Collection $commentables
 *
 * @method static QueryBuilder|EloquentBuilder|$this wasCommentedOn(Model $commentable)
 */
trait CommenterTrait
{
    /**
     * $user->comment($message)->on($post);
     *
     * @param string $message
     *
     * @return Comment
     */
    public function comment($message)
    {
        $comment = new Comment;

        $comment->message        = $message;
        $comment->commenter_id   = $this->getKey();
        $comment->commenter_type = get_class($this);

        return $comment;
    }

    /**
     * @return HasMany
     */
    public function comments()
    {
        $relation = $this->hasMany(Comment::class, 'commenter_id');
        $relation->getQuery()->where('commenter_type', '=', get_class($this));

        return $relation;
    }

    /**
     * @param EloquentBuilder|QueryBuilder $query
     * @param Model                        $commentable
     *
     * @return QueryBuilder
     */
    public function scopeWasCommentedOn($query, Model $commentable)
    {
        $table   = $this->getTable();
        $builder = $query->getQuery();

        $builder->join('comments', 'comments.commenter_id', '=', "{$table}.id")
                ->where('comments.commenter_type', '=', get_class($this))
                ->where('comments.commentable_id', '=', $commentable->getKey())
                ->where('comments.commentable_type', '=', get_class($commentable));

        return $builder;
    }

    /**
     * TODO Optimize performance by reduce SQL query
     *
     * @return array
     */
    public function getCommentablesAttribute()
    {
        $relation = $this->hasMany(Comment::class, 'commenter_id');
        $relation->getQuery()->where('commenter_type', '=', get_class($this));

        return new Collection(array_map(function ($like) {
            return forward_static_call([$like['commentable_type'], 'find'], $like['commentable_id']);
        }, $relation->getResults()->toArray()));
    }
}