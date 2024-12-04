<?php

// Initialize message arrays if they don't exist
if (!isset($success_msg)) {
   $success_msg = [];
}
if (!isset($warning_msg)) {
   $warning_msg = [];
}
if (!isset($info_msg)) {
   $info_msg = [];
}
if (!isset($error_msg)) {
   $error_msg = [];
}

// Handle delete action
if (isset($_POST['delete'])) {
   $delete_id = $_POST['delete_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_delete = $conn->prepare("SELECT * FROM `complaints` WHERE id = ?");
   $verify_delete->execute([$delete_id]);

   if ($verify_delete->rowCount() > 0) {
      $delete_complaint = $conn->prepare("DELETE FROM `complaints` WHERE id = ?");
      $delete_complaint->execute([$delete_id]);
      $success_msg[] = 'Complaint deleted successfully!';
   } else {
      $warning_msg[] = 'Complaint already deleted!';
   }
}

// Handle resolve action
if (isset($_POST['resolve'])) {
   $resolve_id = $_POST['resolve_id'];
   $resolve_id = filter_var($resolve_id, FILTER_SANITIZE_STRING);

   $verify_resolve = $conn->prepare("SELECT * FROM `complaints` WHERE id = ?");
   $verify_resolve->execute([$resolve_id]);

   if ($verify_resolve->rowCount() > 0) {
      $update_resolved = $conn->prepare("UPDATE `complaints` SET status = 'resolved' WHERE id = ?");
      $update_resolved->execute([$resolve_id]);
      $success_msg[] = 'Complaint marked as resolved!';
   } else {
      $warning_msg[] = 'Complaint not found!';
   }
}

// Handle progress action
if (isset($_POST['progress'])) {
   $progress_id = $_POST['progress_id'];
   $progress_id = filter_var($progress_id, FILTER_SANITIZE_STRING);

   $verify_progress = $conn->prepare("SELECT * FROM `complaints` WHERE id = ?");
   $verify_progress->execute([$progress_id]);

   if ($verify_progress->rowCount() > 0) {
      $update_in_progress = $conn->prepare("UPDATE `complaints` SET status = 'in progress' WHERE id = ?");
      $update_in_progress->execute([$progress_id]);
      $success_msg[] = 'Complaint marked as in progress!';
   } else {
      $warning_msg[] = 'Complaint not found!';
   }
}

?>

<!-- Display alerts -->
<?php
if (!empty($success_msg)) {
   foreach ($success_msg as $message) {
      echo "<script>swal('Success!', '{$message}', 'success');</script>";
   }
}

if (!empty($warning_msg)) {
   foreach ($warning_msg as $message) {
      echo "<script>swal('Warning!', '{$message}', 'warning');</script>";
   }
}

if (!empty($info_msg)) {
   foreach ($info_msg as $message) {
      echo "<script>swal('Info!', '{$message}', 'info');</script>";
   }
}

if (!empty($error_msg)) {
   foreach ($error_msg as $message) {
      echo "<script>swal('Error!', '{$message}', 'error');</script>";
   }
}
?>
