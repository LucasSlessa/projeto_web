<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
   exit; // Stop further execution
}

if(isset($_POST['submit'])){
   $id = unique_id();
   $title = filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS);
   $description = filter_var($_POST['description'], FILTER_SANITIZE_SPECIAL_CHARS);
   $status = filter_var($_POST['status'], FILTER_SANITIZE_SPECIAL_CHARS);

   $image = $_FILES['image']['name'];
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id().'.'.$ext;
   $image_folder = '../uploaded_files/'.$rename;

   $add_playlist = $conn->prepare("INSERT INTO `playlist`(id, tutor_id, title, description, thumb, status) VALUES(?,?,?,?,?,?)");
   $add_playlist->execute([$id, $tutor_id, $title, $description, $rename, $status]);

   move_uploaded_file($_FILES['image']['tmp_name'], $image_folder);

   $message[] = 'New playlist created!';  
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Adicione uma Playlist</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="playlist-form">

   <h1 class="heading">criar playlist</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <p>status playlist<span>*</span></p>
      <select name="status" class="box" required>
         <option value="" selected disabled>-- selecione o status</option>
         <option value="active">ativo</option>
         <option value="deactive">inativo</option>
      </select>
      <p>titulo da Playlist <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="titulo da playlist" class="box">
      <p>Descrição da playlist <span>*</span></p>
      <textarea name="description" class="box" required placeholder="escreva uma descrição" maxlength="1000" cols="30" rows="10"></textarea>
      <p>thumbnail <span>*</span></p>
      <input type="file" name="image" accept="image/*" required class="box">
      <input type="submit" value="create playlist" name="submit" class="btn">
   </form>

</section>















<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>