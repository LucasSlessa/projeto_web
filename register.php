<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
   $user_id = $_COOKIE['user_id'];
} else {
   $user_id = '';
}

if (isset($_POST['submit'])) {
   $id = unique_id();
   $name = $_POST['name'];
   $email = $_POST['email'];
   $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT); // Usando password_hash para criptografar a senha
   
   // Renomeando o arquivo de imagem para evitar possíveis problemas de segurança
   $image = $_FILES['image']['name'];
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id() . '.' . $ext;
   
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_files/' . $rename;
   
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);
   
   if ($select_user->rowCount() > 0) {
      $message[] = 'email already taken!';
   } else {
      if ($_POST['pass'] != $_POST['cpass']) {
         $message[] = 'confirm password not matched!';
      } else {
         $insert_user = $conn->prepare("INSERT INTO `users`(id, name, email, password, image) VALUES(?,?,?,?,?)");
         $insert_user->execute([$id, $name, $email, $pass, $rename]);
         move_uploaded_file($image_tmp_name, $image_folder);
         
         // Redirect to login page after successful registration
         header('location: login.php');
         exit; // Adicionando exit para garantir que o script seja interrompido após o redirecionamento
      }
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

   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3>criar conta</h3>
      <div class="flex">
         <div class="col">
            <p>seu nome <span>*</span></p>
            <input type="text" name="name" placeholder="enter your name" maxlength="50" required class="box">
            <p>seu email <span>*</span></p>
            <input type="email" name="email" placeholder="enter your email" maxlength="50" required class="box">
         </div>
         <div class="col">
            <p>sua senha <span>*</span></p>
            <input type="password" name="pass" placeholder="enter your password" maxlength="20" required class="box">
            <p>confirmar senha <span>*</span></p>
            <input type="password" name="cpass" placeholder="confirm your password" maxlength="20" required class="box">
         </div>
      </div>
      <p>selecionar foto <span>*</span></p>
      <input type="file" name="image" accept="image/*" required class="box">
      <p class="link">ja possui uma conta? <a href="login.php">faça login</a></p>
      <input type="submit" name="submit" value="register now" class="btn">
   </form>

</section>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>
