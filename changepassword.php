<?php 
require_once 'init.php';

$validate = new Validate();

$not_required_for_admin = !$user->hasPermissions('admin');

$validate->check($_POST, [
  'current_password' => ['required' => $not_required_for_admin, 'min' => 3],
  'new_password' => ['required' => true, 'min' => 3],
  'new_password_confirm' => ['required' => true, 'min' => 3, 'matches' => 'new_password'],
]);

$db = Database::getInstance();
$person_id = $_GET['id'];
$person = $db->get("users", ["id", "=", $person_id])->first();

if(Input::exists()) {
  if(Token::check(Input::get('token'))) {
    if($validate->passed()) {

      if($user->hasPermissions('admin') || password_verify(Input::get('current_password'), $user->data()->password) ) {
        $user->update(['password' => password_hash(Input::get('new_password'), PASSWORD_DEFAULT)],  $person_id);
        Session::flash('success', 'Password has been updated.');
        Redirect::to("changepassword.php?id=$person_id");
        exit;
      } else {
        echo 'Invalid curent password.';
      }
      
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
            <a href="profile.php" class="nav-link">Профиль</a>
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
         <h1>Изменить пароль</h1>

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
           <li><a href="profile.php?id=<?= $person_id; ?>">Изменить профиль</a></li>
         </ul>
         <form action="" method="POST" class="form">
         <?php if(!$user->hasPermissions('admin')): ?>
           <div class="form-group">
             <label for="current_password">Текущий пароль</label>
             <input name="current_password" type="password" id="current_password" class="form-control">
           </div>
         <?php endif; ?>
           <div class="form-group">
             <label for="new_password">Новый пароль</label>
             <input name="new_password" type="password" id="new_password" class="form-control">
           </div>
           <div class="form-group">
             <label for="new_password_confirm">Повторите новый пароль</label>
             <input name="new_password_confirm" type="password" id="new_password_confirm" class="form-control">
           </div>
           <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">

           <div class="form-group">
             <button class="btn btn-warning">Изменить</button>
           </div>
         </form>


       </div>
     </div>
   </div>
</body>
</html>