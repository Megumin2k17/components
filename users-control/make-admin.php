<?php 

require_once '../init.php';

if(!$user->hasPermissions('admin')) {
    Session::flash('danger', 'У вас недостаточно прав для просмотра данной страницы.');
    Redirect::to('index.php');
    exit;
} else {
	$user->make_admin($_GET['id']);
	$username = $user->find($_GET['id'])->name;
	Session::flash('success', "Пользователь $username получил права админа.");
	Redirect::to('../users_control.php');
	exit;
}

