<?php

include '../components/connect.php';

// Verifica se o tutor está autenticado
if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

// Obtém o ID do vídeo a ser atualizado
if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:dashboard.php');
}

// Tratamento do formulário de atualização
if(isset($_POST['update'])){
   $status = filter_var($_POST['status'], FILTER_SANITIZE_SPECIAL_CHARS);
   $title = filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS);
   $description = filter_var($_POST['description'], FILTER_SANITIZE_SPECIAL_CHARS);
   $playlist = filter_var($_POST['playlist'], FILTER_SANITIZE_SPECIAL_CHARS);

   // Atualização dos arquivos ZIP, thumbnail e vídeo, se enviados
   $zip_folder = '../uploaded_files/';

   // Atualiza o caminho do arquivo ZIP no banco de dados, se enviado
   if(isset($_FILES['new_zip']) && $_FILES['new_zip']['error'] === UPLOAD_ERR_OK){
      $old_zip_file = $_POST['old_zip_file'];

      // Remove o arquivo ZIP antigo, se existir
      if(file_exists($zip_folder.$old_zip_file)){
         unlink($zip_folder.$old_zip_file);
      }

      // Upload do novo arquivo ZIP
      $new_zip_name = $_FILES['new_zip']['name'];
      move_uploaded_file($_FILES['new_zip']['tmp_name'], $zip_folder.$new_zip_name);

      // Atualiza o caminho do arquivo ZIP no banco de dados
      $update_zip = $conn->prepare("UPDATE `content` SET project_folder = ? WHERE id = ?");
      $update_zip->execute([$new_zip_name, $get_id]);
   }

   // Atualiza o status, título e descrição do vídeo no banco de dados
   $update_video = $conn->prepare("UPDATE `content` SET status = ?, title = ?, description = ?, playlist_id = ? WHERE id = ?");
   $update_video->execute([$status, $title, $description, $playlist, $get_id]);

   // Atualização do thumbnail, se enviado
   if(isset($_FILES['thumb']) && $_FILES['thumb']['error'] === UPLOAD_ERR_OK){
      $old_thumb = $_POST['old_thumb'];

      // Remove o thumbnail antigo, se existir
      if(file_exists($zip_folder.$old_thumb)){
         unlink($zip_folder.$old_thumb);
      }

      // Upload do novo thumbnail
      $new_thumb_name = $_FILES['thumb']['name'];
      move_uploaded_file($_FILES['thumb']['tmp_name'], $zip_folder.$new_thumb_name);

      // Atualiza o nome do thumbnail no banco de dados
      $update_thumb = $conn->prepare("UPDATE `content` SET thumb = ? WHERE id = ?");
      $update_thumb->execute([$new_thumb_name, $get_id]);
   }

   // Atualização do vídeo, se enviado
   if(isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK){
      $old_video = $_POST['old_video'];

      // Remove o vídeo antigo, se existir
      if(file_exists($zip_folder.$old_video)){
         unlink($zip_folder.$old_video);
      }

      // Upload do novo vídeo
      $new_video_name = $_FILES['video']['name'];
      move_uploaded_file($_FILES['video']['tmp_name'], $zip_folder.$new_video_name);

      // Atualiza o nome do vídeo no banco de dados
      $update_video_file = $conn->prepare("UPDATE `content` SET video = ? WHERE id = ?");
      $update_video_file->execute([$new_video_name, $get_id]);
   }

   // Mensagem de sucesso...
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Atualizar video</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="video-form">

   <h1 class="heading">Atualizar Projeto</h1>

   <?php
      $select_videos = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND tutor_id = ?");
      $select_videos->execute([$get_id, $tutor_id]);
      if($select_videos->rowCount() > 0){
         while($fecth_videos = $select_videos->fetch(PDO::FETCH_ASSOC)){ 
            $video_id = $fecth_videos['id'];
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="video_id" value="<?= $fecth_videos['id']; ?>">
      <input type="hidden" name="old_thumb" value="<?= $fecth_videos['thumb']; ?>">
      <input type="hidden" name="old_video" value="<?= $fecth_videos['video']; ?>">
      <input type="hidden" name="old_zip_file" value="<?= $fecth_videos['project_folder']; ?>">
      <p>atualizar status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="<?= $fecth_videos['status']; ?>" selected><?= $fecth_videos['status']; ?></option>
         <option value="active">ativo</option>
         <option value="deactive">inativo</option>
      </select>
      <p>Atualizar titulo <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="titulo do projeto" class="box" value="<?= $fecth_videos['title']; ?>">
      <p>Atualizar descriçao<span>*</span></p>
      <textarea name="description" class="box" required placeholder="Escreva a descrição" maxlength="1000" cols="30" rows="10"><?= $fecth_videos['description']; ?></textarea>
      <p>Atualizar Curso</p>
      <select name="playlist" class="box">
         <option value="<?= $fecth_videos['playlist_id']; ?>" selected>--selecionar Curso</option>
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
            echo '<option value="" disabled>nenhum Curso criada!</option>';
         }
         ?>
      </select>
      <img src="../uploaded_files/<?= $fecth_videos['thumb']; ?>" alt="">
      <p>atualizar thumbnail</p>
      <input type="file" name="thumb" accept="image/*" class="box">
      <video src="../uploaded_files/<?= $fecth_videos['video']; ?>" controls></video>
      <p>atualizar video</p>
      <input type="file" name="video" accept="video/*" class="box">
      <p>atualizar arquivo ZIP</p>
      <input type="file" name="new_zip" accept=".zip" class="box">
      <input type="submit" value="Atualizar projeto" name="update" class="btn">
      <div class="flex-btn">
         <a href="view_content.php?get_id=<?= $video_id; ?>" class="option-btn">ver Projeto</a>
         <input type="submit" value="Deletar projeto" name="delete_video" class="delete-btn">
      </div>
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">Projeto nao encontrado! <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">adicionar Projetos</a></p>';
      }
   ?>

</section>


<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>

