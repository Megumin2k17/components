<?php 
require_once 'init.php';



if(Input::exists()) {
  if(Token::check(Input::get('token'))) {
    $validate = new Validate();

    $validation = $validate->check($_POST, [
      'name' => [
        'required' => true,
        'min' => 2,
        'max' => 15       
      ],
      'email' => [
        'required' => true,
        'email' => true,
        'unique' => 'users'     
      ],
      'password' => [
        'required' => true,
        'min' => 3
      ],
      'password_confirm' => [
        'required' => true,
        'matches' => 'password'
      ],
      'rules_agreement' => [
        'required' => true
      ]
    ]);

    if($validation->passed()) {
      $user = new User;
      $user->create([
        'name' => Input::get('name'),
        'email' => Input::get('email'),
        'password' => password_hash(Input::get('password'), PASSWORD_DEFAULT),
        'date' => date('Y-m-d')
      ]);
      Session::flash('success', 'Пользователь был успешно зарегистрирован! <p><a href="index.php">Вернуться на главную страницу</a></p>');
    } else {
      
      $errors = $validation->errors();
      
    }
  }
} else {
  Session::flash('danger', 'Заполните форму.');
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register</title>
	
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
  </head>

  <body class="text-center">
    <form class="form-signin" action="" method="POST">
    	  <img class="mb-4" src="images/apple-touch-icon.png" alt="" width="72" height="72">
    	  <h1 class="h3 mb-3 font-weight-normal">Регистрация</h1>

       
        <?php if($errors): ?>
        <div class="alert alert-danger">
          <ul>
            <?php foreach($errors as $error): ?>
            <li><?= $error; ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <?php if(Session::exists('success')): ?>
        <div class="alert alert-success">
          <?php echo Session::flash('success'); ?>          
        </div>
        <?php elseif(Session::exists('danger')): ?>
          <div class="alert alert-danger">
          <?php echo Session::flash('danger'); ?>          
          </div>
        <?php endif; ?>

    	  <div class="form-group">
          <input name="email" type="email" class="form-control" id="email" placeholder="Email">
        </div>
        <div class="form-group">
          <input name="name" type="text" class="form-control" id="email" placeholder="Ваше имя">
        </div>
        <div class="form-group">
          <input name="password" type="password" class="form-control" id="password" placeholder="Пароль">
        </div>
        
        <div class="form-group">
          <input name="password_confirm" type="password" class="form-control" id="password" placeholder="Повторите пароль">
        </div>

    	  <div class="checkbox mb-3">
    	    <label>
    	      <input name="rules_agreement" type="checkbox"> Согласен со всеми правилами
    	    </label>
    	  </div>

        <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
    	  <button class="btn btn-lg btn-primary btn-block" type="submit">Зарегистрироваться</button>
    	  <p class="mt-5 mb-3 text-muted">&copy; 2017-2020</p>
    </form>
</body>
</html>
