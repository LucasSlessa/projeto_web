<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('location:login.php');
}

if(isset($_POST['submit'])){

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
   $select_user->execute([$user_id]);
   $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

   $prev_pass = $fetch_user['password'];
   $prev_image = $fetch_user['image'];

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);

  if(!empty($name)){
   $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
   $update_name->execute([$name, $user_id]);
   $message[] = 'username updated successfully!';
  }

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_SPECIAL_CHARS);

   if(!empty($email)){
      $select_email = $conn->prepare("SELECT email FROM `users` WHERE email = ?");
      $select_email->execute([$email]);
      if($select_email->rowCount() > 0){
         $message[] = 'email already taken!';
      }else{
         $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
         $update_email->execute([$email, $user_id]);
         $message[] = 'email updated successfully!';
      }
   }

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_SPECIAL_CHARS);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_files/'.$rename;

   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'image size too large!';
      }else{
         $update_image = $conn->prepare("UPDATE `users` SET `image` = ? WHERE id = ?");
         $update_image->execute([$rename, $user_id]);
         move_uploaded_file($image_tmp_name, $image_folder);
         if($prev_image != '' AND $prev_image != $rename){
            unlink('uploaded_files/'.$prev_image);
         }
         $message[] = 'image updated successfully!';
      }
   }

   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
   $old_pass = sha1($_POST['old_pass']);
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_SPECIAL_CHARS);
   $new_pass = sha1($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_SPECIAL_CHARS);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_SPECIAL_CHARS);

   if($old_pass != $empty_pass){
      if($old_pass != $prev_pass){
         $message[] = 'senhas diferentes!';
      }elseif($new_pass != $cpass){
         $message[] = 'confirme suas senhas!';
      }else{
         if($new_pass != $empty_pass){
            $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            $update_pass->execute([$cpass, $user_id]);
            $message[] = 'senha atualizada com sucesso!';
         }else{
            $message[] = 'insira uma nova senha!';
         }
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
   <title>atualizar perfil</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container" style="min-height: calc(100vh - 19rem);">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>atualizar perfil</h3>
      <div class="flex">
         <div class="col">
            <p>seu nome</p>
            <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" maxlength="100" class="box">
            <p>seu email</p>
            <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" maxlength="100" class="box">
            <p>atualizar foto</p>
            <input type="file" name="image" accept="image/*" class="box">
         </div>
         <div class="col">
               <p>senha antiga</p>
               <input type="password" name="old_pass" placeholder="digite sua antiga senha" maxlength="50" class="box">
               <p>nova senha</p>
               <input type="password" name="new_pass" placeholder="digite sua nova senha" maxlength="50" class="box">
               <p>confirmar senha</p>
               <input type="password" name="cpass" placeholder="confirme sua senha" maxlength="50" class="box">
         </div>
      </div>
      <input type="submit" name="submit" value="atualizar" class="btn">
   </form>

</section>

<!-- update profile section ends -->













<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>