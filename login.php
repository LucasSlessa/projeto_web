<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
   $user_id = $_COOKIE['user_id'];
} else {
   $user_id = '';
}

if (isset($_POST['submit'])) {
   $ra = filter_var($_POST['ra'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $nascimento = $_POST['nascimento'];

   $select_user = $conn->prepare("SELECT * FROM `users_validation` WHERE ra = ? LIMIT 1");
   $select_user->execute([$ra]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if ($select_user->rowCount() > 0 && $nascimento == $row['nascimento']) {
      setcookie('user_id', $row['id'], time() + 60*60*24*30, '/');
      if ($row['funcao'] == 'aluno') {
         header('location: home.php');
      } elseif ($row['funcao'] == 'professor') {
         header('location: admin/dashboard.php');
      }
      exit; // Adicionando exit para garantir que o script seja interrompido após o redirecionamento
   } else {
      $message[] = 'RA ou data de nascimento incorretos';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>

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
      <p>Seu RA <span>*</span></p>
      <input type="text" name="ra" placeholder="Digite seu RA" maxlength="20" required class="box">
      <p>Data de nascimento <span>*</p>
      <input type="password" name="nascimento" placeholder="DDMMYYYY" maxlength="8" required class="box">
      <input type="submit" name="submit" value="Login" class="btn">
   </form>

</section>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>
