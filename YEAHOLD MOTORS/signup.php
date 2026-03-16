<?php 
include 'config.php'; 
if (isset($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }

$error = $success = '';

if ($_POST) {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $password  = $_POST['password'];
    $phone     = trim($_POST['phone'] ?? '');

    if (strlen($password) < 6) $error = "Password must be at least 6 characters";
    else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, phone) VALUES (?,?,?,?)");
        try {
            $stmt->execute([$full_name, $email, $hashed, $phone]);
            $_SESSION['success'] = "Account created! Please login.";
            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
            $error = "Email already exists!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up - YEAHOLD MOTORS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
     <background-image: url("bmw-x5.jpg>
<div class="container py-5" background-color: #aa0776;>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow dashboard-card">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Create Account</h2>
                    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="mb-3">
                            <input type="tel" name="phone" class="form-control" placeholder="Phone (optional)">
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password (min 6 chars)" required>
                        </div>
                        <button type="submit" class="btn btn-motor w-100 btn-lg">Sign Up</button>
                    </form>
                    <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>