```
// router.php

use WeRouter\Router;

Router::setNamespace("App\\Controllers\\");


Router::get('/', 'HomeController@home');
Router::get('/article/{id}/{type}', 'HomeController@article');

Router::get('posts/{id}', function ($data) {
    echo $data['id'];
});

Router::run();
```
