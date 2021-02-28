<?php 
require_once 'init.php';

$person_id = $_GET['id'];
if(!$user->hasPermissions('admin') && $user->data()->id !== $person_id) {
    Session::flash('danger', 'У вас недостаточно прав для просмотра данной страницы.');
    Redirect::to('index.php');
    exit;
}


$db = Database::getInstance();
$person = $db->get("users", ["id", "=", $person_id])->first();

$validate = new Validate();

$validate->check($_POST, [
  'name' => ['required' => true, 'min' => 2],
  'status' => ['max' => 111]
]);

if(Input::exists()) {
  if(Token::check(Input::get('token'))) {
    if($validate->passed()) {
      $user->update(['name' => Input::get('name')], $person_id);
      $user->update(['status' => Input::get('status')], $person_id);

      Session::flash('success', 'Данные были усппешно обновлены!');

      Redirect::to("profile.php?id=$person_id");
      exit;
    } else {

      $errors = $validate->errors();  

    }
  }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>
  
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">User Management</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Главная</a>
          </li>
          <?php if($user->hasPermissions('admin')): ?>
          <li class="nav-item">
              <a class="nav-link" href="users_control.php">Управление пользователями</a>
          </li>
          <?php endif; ?>
          </ul>

          <ul class="navbar-nav">
          <?php if(!$user->isLoggedIn()): ?>          
          <li class="nav-item">
            <a href="login.php" class="nav-link">Войти</a>
          </li>
          <li class="nav-item">
            <a href="register.php" class="nav-link">Регистрация</a>
          </li>
          <?php else: ?>
          <li class="nav-item">
            <p class="nav-link">Добро пожаловать, <?= $user->data()->name; ?>! &nbsp;&nbsp;|</p>
          </li>

          <li class="nav-item">
            <a href="profile.php?id=<?= $user->data()->id; ?>" class="nav-link">Профиль</a>
          </li>
          <li class="nav-item">
            <a href="logout.php" class="nav-link">Выйти</a>
          </li>
          <?php endif; ?>          
        </ul>
        </div>
    </nav>

   <div class="container">
     <div class="row">
       <div class="col-md-8">
         <h1>Профиль пользователя - <?= $person->name; ?></h1>

          <?php if(Session::exists('success')): ?>
          <div class="alert alert-success">
            <?php echo Session::flash('success'); ?>          
          </div>
          <?php endif; ?>
         

          <?php if($errors): ?>
          <div class="alert alert-danger">
            <ul>
              <?php foreach($errors as $error): ?>
              <li><?= $error; ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>
         <ul>
           <li><a href="changepassword.php?id=<?= $person->id; ?>">Изменить пароль</a></li>
         </ul>

         <form action="" method="POST" class="form">
           <div class="form-group">
             <label for="username">Имя</label>
             <input name="name" type="text" id="username" class="form-control" value="<?= $person->name; ?>">
           </div>
           <div class="form-group">
             <label for="status">Статус</label>
             <input name="status" type="text" id="status" class="form-control" value="<?= $person->status; ?>">
           </div>
           <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">

           <div class="form-group">
             <button class="btn btn-warning">Обновить</button>
           </div>
         </form>


       </div>
     </div>
   </div>
</body>
</html>