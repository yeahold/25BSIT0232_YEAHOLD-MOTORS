<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Prepare avatar path
$avatar = !empty($user['profile_picture'])
    ? "uploads/avatars/" . htmlspecialchars($user['profile_picture'])
    : "https://via.placeholder.com/180?text=" . urlencode(substr($user['full_name'] ?? 'U', 0, 1));

// Prepare bio display
$bio = !empty($user['bio'])
    ? nl2br(htmlspecialchars($user['bio']))
    : "No description added yet.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile - YEAHOLD MOTORS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-avatar {
            width: 300px;
            height: 400px;
            object-fit: cover;
            border: 5px solid #454646;
            box-shadow: 0 4px 15px rgba(140,102,255,0.25);
        }
        .bio-box {
            background: #5e5c5d;
            width: 700px;
            border-radius: 12px;
            padding: 1.5rem;
            min-height: 140px;
            white-space: pre-wrap;
            border: 1px solid #15cecb;
        }
        .edit-btn {
            position: absolute;
            top: 15px;
            right: 20px;
        }
        .info-icon {
            color: #616063;
        }
        .header {
      background: linear-gradient(135deg, var(--primary), #404141);
      color: black;
      text-align: center;
      padding: 4.5rem 1rem;
       font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
       font: size 30px;
     font-weight: 200;
  
    }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav>
       <bg color: #313131; 
       < div class="header">
            <div class="ms-auto">
                <span class="light green me-3"><b>Welcome My Dear, <?= htmlspecialchars($user['full_name']) ?>!</span>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
                <a href="services.php" class="btn btn-outline-light">Services</a>
                 <a href="brands.php" class="btn btn-outline-light">Brands</a>
                  <a href="gallery.php" class="btn btn-outline-light">Gallery</a>
            </div>
        </div>
        </nav>
     </div>

                    <div class="card-body p-4 p-md-5">

                        <!-- Profile Picture & Basic Info -->
                        <div class="text-center mb-5">
                            <img src="<?= $avatar ?>"
                                 alt="Profile picture of <?= htmlspecialchars($user['full_name']) ?>"
                                 class="rounded rectangle profile-avatar mb-4">
                            <h4 class="mb-1"><?= htmlspecialchars($user['full_name']) ?></h4>
                            <p class="text-muted mb-2">@user<?= $user['id'] ?></p>
                        </div>

                        <!-- Info cards -->
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm">
                                    <i class="fas fa-envelope fa-2x info-icon me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Email</small>
                                        <strong><?= htmlspecialchars($user['email']) ?></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm">
                                    <i class="fas fa-phone fa-2x info-icon me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Phone</small>
                                        <strong><?= htmlspecialchars($user['phone'] ?? 'Not provided') ?></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-white rounded shadow-sm">
                                    <i class="fas fa-calendar-alt fa-2x info-icon me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Member since</small>
                                        <strong><?= date('d M Y', strtotime($user['created_at'])) ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bio Section -->
                        <hr class="my-4">
                        <h5 class="mb-3">
                            <i class="fas fa-info-circle me-2 info-icon"></i>
                          <b>  About Me</b>
                        </h5>
                        <div class="bio-box">
                            <?= $bio ?>
                        </div>

                        <!-- Action Button -->d
                        <div class="text-center mt-5">
                            <button onclick="showAlert()" class="btn btn-motor btn-lg px-5">
                                <i class="fas fa-car me-2"></i> Browse Our Cars
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
    function showAlert() {
        alert("🚗 Welcome to YEAHOLD MOTORS Inventory!\n\nThis section can be expanded later with real car listings.");
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>