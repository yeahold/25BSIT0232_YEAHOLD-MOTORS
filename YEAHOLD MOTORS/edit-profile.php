<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Load current user
$stmt = $pdo->prepare("SELECT full_name, email, phone, profile_picture, bio FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Handle form submission
$success = $error = '';
$upload_dir = 'uploads/avatars/';
$max_size = 2 * 1024 * 1024; // 2 MB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = trim($_POST['bio'] ?? '');

    // Update bio
    $stmt_bio = $pdo->prepare("UPDATE users SET bio = ? WHERE id = ?");
    $stmt_bio->execute([$bio, $user_id]);

    // Handle file upload
    if (!empty($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_picture'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed)) {
            $error = "Allowed formats: JPG, JPEG, PNG, WEBP";
        } elseif ($file['size'] > $max_size) {
            $error = "File too large (max 2 MB)";
        } else {
            $new_name = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
            $destination = $upload_dir . $new_name;

            // Make sure folder exists (extra safety)
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Update database
                $stmt_pic = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                $stmt_pic->execute([$new_name, $user_id]);
                $success = "Profile picture and bio updated successfully!";
            } else {
                $error = "Failed to save image. Check folder permissions.";
            }
        }
    } elseif (empty($error)) {
        $success = "Bio updated successfully!";
    }

    // Reload fresh data
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}

// Prepare avatar for display
$avatar_path = !empty($user['profile_picture']) && file_exists($upload_dir . $user['profile_picture'])
    ? $upload_dir . htmlspecialchars($user['profile_picture'])
    : "https://via.placeholder.com/220?text=" . urlencode(substr($user['full_name'] ?? 'U', 0, 1));

$bio_display = htmlspecialchars($user['bio'] ?? '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - YEAHOLD MOTORS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-avatar-preview {
            width: 220px;
            height: 220px;
            object-fit: cover;
            border: 6px solid #8c66ff;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(140,102,255,0.3);
        }
        textarea { min-height: 160px; resize: vertical; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">YEAHOLD MOTORS</a>
        <div class="ms-auto">
            <a href="dashboard.php" class="btn btn-outline-light me-2">Back</a>
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0">Edit Profile</h3>
                </div>

                <div class="card-body p-5">

                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="text-center mb-5">
                        <img src="<?= $avatar_path ?>" class="profile-avatar-preview mb-3" alt="Current profile picture">
                        <h4><?= htmlspecialchars($user['full_name']) ?></h4>
                    </div>

                    <form method="POST" enctype="multipart/form-data">

                        <div class="mb-4">
                            <label class="form-label fw-bold">New Profile Picture</label>
                            <input type="file" name="profile_picture" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <small class="form-text text-muted">JPG, JPEG, PNG, WEBP • max 2 MB</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">About Me</label>
                            <textarea name="bio" class="form-control" rows="5" placeholder="Tell others about yourself..."><?= $bio_display ?></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>