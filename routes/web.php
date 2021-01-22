<?php

use App\Controllers\TodoController;

$app->get('/logout', function($request, $response){
		session_start();
		//unset($_SESSION['username']);
		//unset($_SESSION['email']);
		session_destroy();
		return $this->view->render($response, 'mytodos.twig');
})->setName('logout');


$app->group('/', function(){

    //this route takes you to home page
    $this->get('', function($request, $response){
        session_start();

        if(!isset($_SESSION['username']))
        {
            return $this->view->render($response, 'mytodos.twig');
        }
        else
        {
            return $this->view->render($response, 'home.twig');
        }
        
    })->setName('home');

    //this route handle the post of adding new todo
    $this->post('', TodoController::class. ':addTodo')->setName('home');


    //this route handle the view of inputing useranme to view personal todos
    $this->get('/mytodos', function($request, $response){
        return $this->view->render($response, 'mytodos.twig');
    })->setName('mytodos');

    //this route recieved the post from mytodos and process it
    $this->post('/mytodos', TodoController::class. ':viewTodo')->setName('mytodos');

    $this->get('/edit[/{id}]', TodoController::class. ':editView')->setName('edit');

    $this->post('/edit[/{id}]', TodoController::class. ':editTodo')->setName('edit');

    $this->get('/delete[/{id}]', TodoController::class. ':deleteTodo')->setName('delete');

    $this->get('/share', function($request, $response){
        echo 'whatapp | Facebook | twitter | instagram | Linkedin';
    })->setName('share');
	
	

});


