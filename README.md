# Getting Started

Provide an _elegant way_ to interact with comment feature between your eloquent models.

**Note**: The package is only support Laravel 5

# Installation

**Step 1**: Install package
```bash
composer require namest/commentable
```

**Step 2**: Register service provider in your `config/app.php`
```php
return [
    ...
    'providers' => [
        ...
        'Namest\Commentable\CommentableServiceProvider',
    ],
    ...
];
```

**Step 3**: Publish package resources, include: configs, migrations. Open your terminal and type:
```bash
php artisan vendor:publish --provider="Namest\Commentable\CommentableServiceProvider"
```

**Step 4**: Migrate the migration that have been published
```bash
php artisan migrate
```

**Step 5**: Use some traits to make awesome things
```php
class User extends Model
{
    use \Namest\Commentable\CommenterTrait;
    
    // ...
}

class Post extends Model
{
    use \Namest\Commentable\CommentableTrait;
    
    // ...
}
```

**Step 6**: Read API below and start _happy_

# API

```php
$user = \App\User::find(1);
$post = \App\Post::find(1);

$comment = $user->comment('Awesome')->about($post); // Return \Namest\Commentable\Comment instance
```

```php
$users = \App\User::wasCommentedOn($post); // Return all user was commented on a post
$posts = \App\Post::hasCommentBy($user); // Get all posts that the user was leave comment on
```

```php
$comments = \Namest\Commentable\Comment::by($user); // Return all comments that the user was leave
$comments = \Namest\Commentable\Comment::by($user, 'App\Post'); // Same as above but filter only comments on posts
```

```php
$user->comments; // Return all comments that the user was leave
$user->commentables; // Return all commentable (in all types)
$post->commenters; // Return all commenters (in all types)
```

# Censoring

Set up censoring options in `config/commentable.php` file

# Events

#### namest.commentable.prepare

When: Parepare for `$commenter` to leave a comment on the commentable and but before event `namest.commentable.commenting`

Payloads:
- `$commenter`: Who do this action
- `$message`: Comment message

Usage:
```php
\Event::listen('namest.commentable.prepare', function ($commenter, $message) {
    // Do something
});
```

#### namest.commentable.commenting

When: Before the `$commenter` leave a comment on the commentable but after the event `namest.commentable.prepare`

Payloads:
- `$commentable`: Which receive a comment from commenter
- `$message`: Comment message

Usage:
```php
\Event::listen('namest.commentable.commenting', function ($commentable, $message) {
    // Do something
});
```

#### namest.commentable.commented

When: After the `$commenter` left a comment on the commentable

Payloads:
- `$comment`: Comment instance (you can get commenter & commentable from comment instance by `$comment->commenter` & `$comment->commentable`)

Usage:
```php
\Event::listen('namest.commentable.commented', function ($comment) {
    // Do something
});
```