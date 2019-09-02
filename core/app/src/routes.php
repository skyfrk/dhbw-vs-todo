<?php

use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Controllers\TaskListController;
use App\Controllers\TaskController;
use App\Controllers\ReportController;
use App\Controllers\UserController;

$app->group('/api', function () {

    // Auth routes
    $this->post('/register', RegisterController::class . ':register');
    $this->get('/register/confirm/{token}', RegisterController::class . ':confirm');
    $this->post('/register/resend', RegisterController::class . ':resend');
    $this->post('/login', LoginController::class . ':login');

    // Protected routes
    $this->group('', function () {

        // Auth
        $this->post('/login/renew', LoginController::class . ':renew');

        // User
        $this->get('/user/tokens', UserController::class . ':getValidJwts');
        $this->post('/user/token/{id}', UserController::class . ':invalidateJwt');

        // TaskList routes
        $this->get('/taskLists', TaskListController::class . ':getTaskLists');
        $this->post('/taskLists', TaskListController::class . ':createTaskList');
        $this->get('/taskLists/{id}', TaskListController::class . ':getTaskList');
        $this->put('/taskLists/{id}', TaskListController::class . ':updateTaskList');
        $this->delete('/taskLists/{id}', TaskListController::class . ':deleteTaskList');
        $this->get('/taskLists/{id}/tasks', TaskListController::class . ':getTasks');
        $this->post('/taskLists/{id}/tasks', TaskListController::class . ':createTask');
        $this->get('/taskLists/{id}/report', TaskListController::class . ':getReport');

        // Task routes
        $this->get('/tasks', TaskController::class . ':getTasks');
        $this->get('/tasks/{id}', TaskController::class . ':getTask');
        $this->put('/tasks/{id}', TaskController::class . ':updateTask');
        $this->delete('/tasks/{id}', TaskController::class . ':deleteTask');

        // Report routes
        $this->get('/report', ReportController::class . ':getReport');

    })->add($this->getContainer()->get('authMiddleware'))->add($this->getContainer()->get('jwtMiddleware'));
});

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler;
    return $handler($req, $res);
});
