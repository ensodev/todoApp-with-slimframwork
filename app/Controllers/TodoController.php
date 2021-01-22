<?php

namespace App\Controllers;

//use App\Controllers\Controller;
use PDO;

class TodoController extends Controller
{

    protected $todo;
    protected $username;
    protected $email;
    protected $message;
    protected $date_created;
    protected $todo_date;
       
    public function addTodo($request, $response)
    {
        //session_start();

        $todo = $request->getParam('todo');
        $username = $request->getParam('username');
        $email = $request->getParam('email');
        $message = $request->getParam('messsage');
        $date_created = date('U');
        
		//getting date and converting it to timestamp
        $dt = $request->getParam('date');;
        $date = date_create($dt);
        $todo_date = date_format($date, 'U');

        if(isset($_SESSION['username']) AND isset($_SESSION['email']))
        {
            if($_SESSION['username'] != $username OR $_SESSION['email'] != $email )
            {   
                $msg = true;
                $msg_info = 'You are not authorized to add to this present user todo list, Close your browser and start it again or use another browser';
                return $this->c->view->render($response, 'updated.twig', compact('msg', 'msg_info'));
                exit();
            }
        }

        $todos = $this->c->db->prepare("INSERT INTO todo (topic, username, email, messagea, date_created, todo_date) VALUES(:topic, :username, :email, :messagea, :date_created, :todo_date)");

        $todos->bindParam(':topic', $todo);
        $todos->bindParam(':username', $username);
        $todos->bindParam(':email', $email);
        $todos->bindParam(':messagea', $message);
        $todos->bindParam(':date_created', $date_created);
        $todos->bindParam(':todo_date', $todo_date);

        $todos->execute();
        
        //$todos->execute(['topic'=>$todo, 'username'=>$username, 'email'=>$email, 'messagea'=>$message, 'date_created'=>$date_created, 'todo_date'=>$todo_date]);

        if($todo)
        {
            $msg = 1;
        }
        else
        {
            $msg = 0;
        }

        return $this->c->view->render($response, 'home.twig', compact('msg', 'username'));
    }

    public function viewTodo($request, $response)
    {   
        //session_start();
        
        $username = $request->getParam('username');
        $email = $request->getParam('email');
		
		if(!isset($_SESSION['username']) AND !isset($_SESSION['email']))
        {
			$_SESSION['username'] = $username;
			$_SESSION['email'] = $email;
        }


        if(isset($_SESSION['username']) AND isset($_SESSION['email']))
        {
            if($_SESSION['username'] != $username OR $_SESSION['email'] != $email )
            {   
                $msg = true;
                $msg_info = 'You are not authorized to view todo list, Close your browser and start it again or use another browser';
                return $this->c->view->render($response, 'updated.twig', compact('msg', 'msg_info'));
                exit();
            }
        }


        $todos = $this->c->db->prepare("SELECT * FROM todo WHERE username =:username AND email =:email ORDER BY todo_date");
        $todos->execute(['username'=>$username, 'email'=>$email]);
        $todos = $todos->fetchAll(PDO::FETCH_OBJ);

        

        if (!$todos){
            $msg = false;
            return $this->c->view->render($response, 'todolist.twig', compact('msg'));
            exit();
        }

        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

        $msg = true;
		$date = date("U");
		
		//var_dump($date);
		//var_dump($todos);
		//die();
		
        return $this->c->view->render($response, 'todolist.twig', compact('todos', 'msg', 'date'));
    }

    public function editView($request, $response, $args)
    {
        $id = $args['id'];

        $todos = $this->c->db->prepare("SELECT * FROM todo WHERE id =:id");
        $todos->execute(['id'=>$id]);
        $todos = $todos->fetch(PDO::FETCH_OBJ);

        if (!$todos){
            $msg = false;
            return $this->c->view->render($response, 'edittodo.twig', compact('msg'));
            exit();
        }

        $msg = true;
        // var_dump($todos);
        // die();
        return $this->c->view->render($response, 'edittodo.twig', compact('todos', 'msg'));
        
    }

    public function editTodo($request, $response)
    {   
        //session_start();
        $id = $request->getParam('id');
        $todo = $request->getParam('todo');
        $email = $request->getParam('email');
        $message = $request->getParam('message');
        $todo_date = $request->getParam('date');

        $todos = $this->c->db->prepare("UPDATE todo SET topic =:topic, messagea =:messagea, todo_date =:todo_date WHERE id =:id AND email =:email");
        $todos->execute(['topic'=>$todo, 'messagea'=>$message, 'todo_date'=>$todo_date, 'id'=>$id, 'email'=>$email]);

        // $msg = true;
        // $msg_info ='Todo Edited what will you like to do';

        //.....active
        $username = $_SESSION['username'];
        $email = $_SESSION['email'];

        $todos = $this->c->db->prepare("SELECT * FROM todo WHERE username =:username AND email =:email");
        $todos->execute(['username'=>$username, 'email'=>$email]);
        $todos = $todos->fetchAll(PDO::FETCH_OBJ);
       
        //$msg = true;
        return $this->c->view->render($response, 'todolist.twig', compact('todos'));
        
        //return $this->c->view->render($response, 'updated.twig', compact('msg', 'msg_info'));
    }

    public function deleteTodo($request, $response, $args)
    {
       //session_start();

       $id = $args['id'];

       $todos = $this->c->db->prepare("DELETE FROM todo WHERE id =:id");
       $todos->execute(['id'=>$id]);

       $msg = true;
       $msg_info ='Todo deleted what will you like to do';

       //.................

       $username = $_SESSION['username'];
       $email = $_SESSION['email'];

       $todos = $this->c->db->prepare("SELECT * FROM todo WHERE username =:username AND email =:email");
       $todos->execute(['username'=>$username, 'email'=>$email]);
       $todos = $todos->fetchAll(PDO::FETCH_OBJ);
      
       //$msg = true;
       return $this->c->view->render($response, 'todolist.twig', compact('todos'));

       //return $this->c->view->render($response, 'updated.twig', compact('msg', 'msg_info'));
       
    }

}