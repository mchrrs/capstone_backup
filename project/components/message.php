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

// Show success messages
if (!empty($success_msg) && is_array($success_msg)) {
   foreach ($success_msg as $msg) {
      echo '<script>swal("' . $msg . '", "", "success");</script>';
   }
}

// Show warning messages
if (!empty($warning_msg) && is_array($warning_msg)) {
   foreach ($warning_msg as $msg) {
      echo '<script>swal("' . $msg . '", "", "warning");</script>';
   }
}

// Show info messages
if (!empty($info_msg) && is_array($info_msg)) {
   foreach ($info_msg as $msg) {
      echo '<script>swal("' . $msg . '", "", "info");</script>';
   }
}

// Show error messages
if (!empty($error_msg) && is_array($error_msg)) {
   foreach ($error_msg as $msg) {
      echo '<script>swal("' . $msg . '", "", "error");</script>';
   }
}
?>

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
?>
