```
// router.php

use WeRouter\Router;

Router::setNamespace("App\\Controllers\\");


Router::get('/', 'HomeController@home');
Router::get('/article/{id}/{type}', 'HomeController@article');

Router::get('posts/{id}', function ($data) {
    echo $data['id'];
});

Router::posts('/posts/create', 'PostController@create');

Router::posts('/posts/create/{type}', function ($data) {
    echo $data['type'] . "\n";
    echo $data['input'];
});


Router::run();
```
