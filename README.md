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

# Censoring

Set up censoring options in `config/commentable.php` file


