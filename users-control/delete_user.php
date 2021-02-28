<?php 

require_once '../init.php';

if(!$user->hasPermissions('admin')) {
    Session::flash('danger', 'У вас недостаточно прав для просмотра данной страницы.');
    Redirect::to('index.php');
    exit;
}

$person_id = $_GET['id'];
$username = $user->find($person_id)->name;

if($user->find($person_id)) {	
	User::delete_user($person_id);
	Session::flash('info', "Пользователь $username, был удалён.");
	Redirect::to('../users_control.php');
	exit;
} else {
	Redirect::to('../users_control.php');
	Session::flash('danger', "Пользователь с именем $username, не был найден.");
    exit;
}
