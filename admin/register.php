<?php
include '../components/connect.php';

if(isset($_POST['submit'])){

   $id = unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
   $profession = $_POST['profession'];
   $profession = filter_var($profession, FILTER_SANITIZE_SPECIAL_CHARS);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_EMAIL);
   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];

   // Verificar se as senhas coincidem
   if($pass != $cpass){
      $message[] = 'senhas diferentes';
   }else{
      // Criptografar a senha usando password_hash
      $passHash = password_hash($pass, PASSWORD_DEFAULT);

      $insert_tutor = $conn->prepare("INSERT INTO `tutors`(id, name, profession, email, password) VALUES(?,?,?,?,?)");
      $insert_tutor->execute([$id, $name, $profession, $email, $passHash]);
      $message[] = 'registrado com sucesso, por favor faça login!';
   }

}

?>



<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>registro</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body style="padding-left: 0;">

<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message form">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<!-- register section starts  -->

<section class="form-container">

   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3>novo registro</h3>
      <div class="flex">
         <div class="col">
            <p>seu nome <span>*</span></p>
            <input type="text" name="name" placeholder="insira seu nome" maxlength="50" required class="box">
            <p>sua profissao <span>*</span></p>
            <select name="profession" class="box" required>
               <option value="" disabled selected>-- selecione sua area</option>
               <option value="developer">Desenvolvimento</option>
               <option value="desginer">Arquitetrua</option>
               <option value="musician">musica</option>
               <option value="biologist">biologia</option>
               <option value="teacher">Engenharia</option>
               <option value="engineer">Advocacia</option>
               <option value="lawyer">Astrofisica</option>
               <option value="accountant">Economia</option>
               <option value="doctor">Veterinaria</option>
               <option value="journalist">jornalista</option>
               <option value="photographer">Cinema</option>
            </select>
            <p>seu email <span>*</span></p>
            <input type="email" name="email" placeholder="insira seu email" maxlength="50" required class="box">
         </div>
         <div class="col">
            <p>sua senha <span>*</span></p>
            <input type="password" name="pass" placeholder="insira sua senha" maxlength="20" required class="box">
            <p>confirme sua senha <span>*</span></p>
            <input type="password" name="cpass" placeholder="confirme sua senha" maxlength="20" required class="box">
            <p>selecione uma foto <span>*</span></p>
            <input type="file" name="image" accept="image/*" required class="box">
         </div>
      </div>
      <p class="link">ja possui uma conta?! <a href="login.php">faça login</a></p>
      <input type="submit" name="submit" value="register now" class="btn">
   </form>

</section>

<!-- registe section ends -->
</body>
</html>
