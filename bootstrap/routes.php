<?php

return [
    ['POST', '/login', ['Framework\Controllers\AuthController', 'Login']],
    ['POST', '/register', ['Framework\Controllers\AuthController', 'Register']],
    ///////////////////////////////////////////////////////////////////////////
    ['GET', '/user/{id:\d+}', ['Framework\Controllers\UserController', 'GetUserById']],
    ['POST', '/user/{id:\d+}/edit', ['Framework\Controllers\UserController', 'PostUserEdit']],
    ['GET', '/user/search', ['Framework\Controllers\UserController', 'GetUserSearch']],
    ['POST', '/user/{id:\d+}/follow', ['Framework\Controllers\UserController', 'PostUserFollow']],
    ['POST', '/user/{id:\d+}/unfollow', ['Framework\Controllers\UserController', 'PostUserUnFollow']],
    /////////////////////////////////////////////////////////////////////////////
    ['POST', '/statuses/create', ['Framework\Controllers\StatusesController', 'PostStatusesCreate']],
    ['GET', '/statuses/{id:\d+}', ['Framework\Controllers\StatusesController', 'GetStatusesById']],
    ['GET', '/statuses/home', ['Framework\Controllers\StatusesController', 'GetStatusesHome']],
    ['GET', '/statuses/user-timeline', ['Framework\Controllers\StatusesController', 'GetStatusesUserTimeline']],
];
