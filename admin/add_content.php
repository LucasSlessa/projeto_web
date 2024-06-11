<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

if(isset($_POST['submit'])){
   $id = unique_id();
   $status = filter_var($_POST['status'], FILTER_SANITIZE_SPECIAL_CHARS);
   $title = filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS);
   $description = filter_var($_POST['description'], FILTER_SANITIZE_SPECIAL_CHARS);
   $playlist = filter_var($_POST['playlist'], FILTER_SANITIZE_SPECIAL_CHARS);

   // Process thumbnail
   $thumb = $_FILES['thumb']['name'];
   $thumb = filter_var($thumb, FILTER_SANITIZE_SPECIAL_CHARS);
   $thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION);
   $rename_thumb = unique_id().'.'.$thumb_ext;
   $thumb_size = $_FILES['thumb']['size'];
   $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
   $thumb_folder = '../uploaded_files/'.$rename_thumb;

   // Process video
   $video = $_FILES['video']['name'];
   $video = filter_var($video, FILTER_SANITIZE_SPECIAL_CHARS);
   $video_ext = pathinfo($video, PATHINFO_EXTENSION);
   $rename_video = unique_id().'.'.$video_ext;
   $video_tmp_name = $_FILES['video']['tmp_name'];
   $video_folder = '../uploaded_files/'.$rename_video;

   // Process project folder (zip file)
   $project_zip = $_FILES['project_zip']['name'];
   $project_zip = filter_var($project_zip, FILTER_SANITIZE_SPECIAL_CHARS);
   $zip_ext = pathinfo($project_zip, PATHINFO_EXTENSION);
   $rename_zip = unique_id().'.'.$zip_ext;
   $zip_tmp_name = $_FILES['project_zip']['tmp_name'];
   $zip_folder = '../uploaded_files/'.$rename_zip;

   if($thumb_size > 2000000){
      $message[] = 'imagem muito grande!';
   }else{
      $add_playlist = $conn->prepare("INSERT INTO `content`(id, tutor_id, playlist_id, title, description, video, thumb, status, project_folder) VALUES(?,?,?,?,?,?,?,?,?)");
      $add_playlist->execute([$id, $tutor_id, $playlist, $title, $description, $rename_video, $rename_thumb, $status, $rename_zip]);
      move_uploaded_file($thumb_tmp_name, $thumb_folder);
      move_uploaded_file($video_tmp_name, $video_folder);
      move_uploaded_file($zip_tmp_name, $zip_folder);
      $message[] = 'novo projeto enviado!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="video-form">
   <h1 class="heading">Enviar Projeto</h1>
   <form action="" method="post" enctype="multipart/form-data">
      <p>status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="" selected disabled>-- Selecione o Status</option>
         <option value="active">ativo</option>
         <option value="inative">inativo</option>
      </select>
      <p>titulo do Projeto <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="titulo do projeto" class="box">
      <p>Descrição do Projeto<span>*</span></p>
      <textarea name="description2" class="box" required placeholder="escreva uma descrição" maxlength="1000" cols="30" rows="10"></textarea>
      <p>Resumo do Projeto<span>*</span></p>
      <textarea name="description" class="box" required placeholder="Resumo do projeto" maxlength="10000" cols="30" rows="10"></textarea>
      <p>Tipo do Projeto <span>*</span></p>
            <select name="tipo" class="box" required>
               <option value="" disabled selected>-- selecione o tipo</option>
               <option value="desenvolvimento">TCC(Tese de conclusao de curso)</option>
               <option value="arquitetura">Evento</option>
               <option value="musica">Projeto</option>
               

            </select>
      <p>Curso<span>*</span></p>
      <select name="playlist" class="box" required>
         <option value="" disabled selected>--selecione um Curso</option>
         <?php
         $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
         $select_playlists->execute([$tutor_id]);
         if($select_playlists->rowCount() > 0){
            while($fetch_playlist = $select_playlists->fetch(PDO::FETCH_ASSOC)){
         ?>
         <option value="<?= $fetch_playlist['id']; ?>"><?= $fetch_playlist['title']; ?></option>
         <?php
            }
         ?>
         <?php
         }else{
            echo '<option value="" disabled>nenhum curso criado!</option>';
         }
         ?>
      </select>
      <p>Thumbnail<span>*</span></p>
      <input type="file" name="thumb" accept="image/*" required class="box">
      <p>Video <span>*</span></p>
      <input type="file" name="video" accept="video/*" required class="box">
      <p>Zip do Projeto <span>*</span></p>
      <input type="file" name="project_zip" accept=".zip" required class="box">
      <input type="submit" value="enviar projeto" name="submit" class="btn">
   </form>
</section>

<?php include '../components/footer.php'; ?>
<script src="../js/admin_script.js"></script>
</body>
</html>
