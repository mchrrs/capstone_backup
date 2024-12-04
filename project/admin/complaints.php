<?php

include '../components/connect.php';

if (isset($_COOKIE['admin_id'])) {
   $admin_id = $_COOKIE['admin_id'];
} else {
   $admin_id = '';
   header('location:login.php');
}

// Delete complaint logic
if (isset($_POST['delete'])) {
   $delete_id = $_POST['delete_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_delete = $conn->prepare("SELECT * FROM `complaints` WHERE id = ?");
   $verify_delete->execute([$delete_id]);

   if ($verify_delete->rowCount() > 0) {
      $delete_complaint = $conn->prepare("DELETE FROM `complaints` WHERE id = ?");
      $delete_complaint->execute([$delete_id]);
      $success_msg[] = 'Complaint deleted!';
   } else {
      $warning_msg[] = 'Complaint already deleted!';
   }
}

// Update status to 'resolved'
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

// Update status to 'in progress'
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

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Complaints</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>
   <style>
      .box-container {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
         gap: 20px;
      }

      .box {
         background-color: #fff;
         border-radius: 10px;
         box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
         padding: 20px;
         transition: all 0.3s ease;
      }

      .box:hover {
         box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
      }

      .box p {
         margin: 10px 0;
         font-size: 1rem;
      }

      .status-badge {
         padding: 5px 10px;
         border-radius: 5px;
         font-weight: bold;
         text-transform: capitalize;
      }

      .status-badge.pending {
         background-color: #ffc107;
         color: #fff;
      }

      .status-badge.resolved {
         background-color: #28a745;
         color: #fff;
      }

      .status-badge.in-progress {
         background-color: #17a2b8;
         color: #fff;
      }

      .action-form {
         display: inline-block;
         margin-right: 10px;
      }

      .action-form button {
         padding: 10px 15px;
         border: none;
         border-radius: 5px;
         font-size: 0.9rem;
         cursor: pointer;
         transition: all 0.3s ease;
      }

      .delete-btn {
         background-color: #dc3545;
         color: #fff;
      }

      .delete-btn:hover {
         background-color: #c82333;
      }

      .resolve-btn {
         background-color: #28a745;
         color: #fff;
      }

      .resolve-btn:hover {
         background-color: #218838;
      }

      .progress-btn {
         background-color: #007bff;
         color: #fff;
      }

      .progress-btn:hover {
         background-color: #0056b3;
      }

      .empty {
         text-align: center;
         color: #777;
         font-size: 1.2rem;
      }
   </style>
   <!-- header section starts  -->
   <?php include '../components/admin_header.php'; ?>
   <!-- header section ends -->

   <!-- complaints section starts  -->

   <section class="complaint-details">

      <h1 class="heading">complaints</h1>

      <form action="" method="POST" class="search-form">
         <input type="text" name="search_box" placeholder="search complaints..." maxlength="100" required>
         <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>

      <?php
      if (isset($_POST['search_box']) || isset($_POST['search_btn'])) {
         $search_box = $_POST['search_box'];
         $search_box = filter_var($search_box, FILTER_SANITIZE_STRING);
         $select_complaints = $conn->prepare("
        SELECT c.*, u.name AS user_name 
        FROM `complaints` c 
        JOIN `users` u ON c.user_id = u.id 
        WHERE c.description LIKE ? OR c.status LIKE ? OR u.name LIKE ?
        ");
         $select_complaints->execute(["%{$search_box}%", "%{$search_box}%", "%{$search_box}%"]);
      } else {
         $select_complaints = $conn->prepare("
        SELECT c.*, u.name AS user_name 
        FROM `complaints` c 
        JOIN `users` u ON c.user_id = u.id
        ");
         $select_complaints->execute();
      }
      ?>

      <table class="occupied-properties-table">
         <thead>
            <tr>
               <th>User Name</th>
               <th>Complaint Type</th>
               <th>Description</th>
               <th>Status</th>
               <th>Submitted At</th>
               <th>Actions</th>
            </tr>
         </thead>
         <tbody>
            <?php
            if ($select_complaints->rowCount() > 0) {
               while ($fetch_complaints = $select_complaints->fetch(PDO::FETCH_ASSOC)) {
            ?>
                  <tr>
                     <td><?= htmlspecialchars($fetch_complaints['user_name']); ?></td>
                     <td><?= htmlspecialchars($fetch_complaints['complaint_type']); ?></td>
                     <td><?= htmlspecialchars($fetch_complaints['description']); ?></td>
                     <td>
                        <span class="status-badge <?= strtolower($fetch_complaints['status']); ?>">
                           <?= htmlspecialchars($fetch_complaints['status']); ?>
                        </span>
                     </td>
                     <td><?= htmlspecialchars($fetch_complaints['submitted_at']); ?></td>
                     <td>
                        <form action="" method="POST" class="action-form">
                           <input type="hidden" name="delete_id" value="<?= htmlspecialchars($fetch_complaints['id']); ?>">
                           <button type="submit" onclick="return confirm('Delete this complaint?');" name="delete" class="delete-btn">Delete</button>
                        </form>
                        <form action="" method="POST" class="action-form">
                           <input type="hidden" name="resolve_id" value="<?= htmlspecialchars($fetch_complaints['id']); ?>">
                           <button type="submit" name="resolve" class="resolve-btn">Mark as Resolved</button>
                        </form>
                        <form action="" method="POST" class="action-form">
                           <input type="hidden" name="progress_id" value="<?= htmlspecialchars($fetch_complaints['id']); ?>">
                           <button type="submit" name="progress" class="progress-btn">Mark as In Progress</button>
                        </form>
                     </td>
                  </tr>
            <?php
               }
            } else {
               echo '<tr><td colspan="6" class="empty">No complaints found!</td></tr>';
            }
            ?>
         </tbody>
      </table>

   </section>
   <?php include '../components/message.php'; ?>


</body>

</html>