<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => 'api/user'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('sign-in', 'AuthController@login');

    $router->post('recover-password', 'AuthController@generateRecoveryToken');
    $router->patch('recover-password', 'AuthController@updatePasswordByRecoveryToken');

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->get('companies', 'CompaniesController@index');
        $router->post('companies', 'CompaniesController@create');
    });
});
