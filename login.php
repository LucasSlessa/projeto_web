<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
   $user_id = $_COOKIE['user_id'];
} else {
   $user_id = '';
}

if (isset($_POST['submit'])) {

   $email = $_POST['email'];
   $email = filter_var($_POST['email'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $password = $_POST['pass'];

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? LIMIT 1");
   $select_user->execute([$email]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);
   
   if ($select_user->rowCount() > 0 && password_verify($password, $row['password'])) {
      setcookie('user_id', $row['id'], time() + 60*60*24*30, '/');
      header('location: home.php');
      exit; // Adicionando exit para garantir que o script seja interrompido após o redirecionamento
   } else {
      $message[] = 'senha ou email incorretos';
   }

  
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data" class="login">
      <h3>Bem vindo de volta!</h3>
      <p>seu email <span>*</span></p>
      <input type="email" name="email" placeholder="digite seu email" maxlength="50" required class="box">
      <p>sua senha <span>*</span></p>
      <input type="password" name="pass" placeholder="digite sua senha" maxlength="20" required class="box">
      <p class="link">não possui uma conta? <a href="register.php">registre-se agora</a></p>
      <input type="submit" name="submit" value="login now" class="btn">
   </form>

</section>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>
