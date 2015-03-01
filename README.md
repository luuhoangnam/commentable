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
