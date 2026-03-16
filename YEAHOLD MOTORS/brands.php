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

// Array of car brands – easy to add/remove/edit
$brands = [
    [
        'name' => 'Toyota',
        'description' => 'The most trusted & dominant brand in Uganda – Hilux, Noah, Prado, Corolla, Land Cruiser & more. Reliable, durable, high resale value.',
        'logo' => 'https://global.toyota/pages/global_toyota/mobility/toyota-brand/emblem_ogp_001.png',
        'image' => 'https://www.netcarshow.com/Toyota-Hilux_Special_Edition-2019-Front.28bc40f5.jpg',
        'popular_models' => 'Hilux, Noah, Land Cruiser Prado, Corolla, RAV4'
    ],
    [
        'name' => 'Nissan',
        'description' => 'Strong pickups & reliable family cars – NP300 Hardbody, Navara, X-Trail, Patrol. Great for business & tough roads.',
        'logo' => 'https://www.carlogos.org/car-logos/nissan-logo-1984-2000x1400.png',
        'image' => 'https://www.netcarshow.com/photos/2022-Nissan_Navara_Pro-4X_Warrior-2022-1600x1200.jpg', // example Navara
        'popular_models' => 'NP300 Hardbody, Navara, X-Trail, Patrol'
    ],
    [
        'name' => 'Mitsubishi',
        'description' => 'Rugged & spacious – Delica, Pajero, L200 Triton. Very popular for families and off-road use in Uganda.',
        'logo' => 'https://www.carlogos.org/car-brands-logos/mitsubishi-logo-2000x1400.png', // common link
        'image' => 'https://imgcdn.zigwheels.my/medium/gallery/exterior/1/23/mitsubishi-delica-2020-front-angle-low-view-92939.jpg',
        'popular_models' => 'Delica, Pajero, L200 Triton'
    ],
    [
        'name' => 'Subaru',
        'description' => 'All-wheel drive excellence – Forester, Outback, Impreza. Loved for safety & performance on wet/rough roads.',
        'logo' => 'https://www.carlogos.org/car-brands-logos/subaru-logo-2000x1400.png',
        'image' => 'https://www.subaru.com/content/dam/subaru/vehicles/2025/forester/gallery/exterior/2025-subaru-forester-front-three-quarter.jpg',
        'popular_models' => 'Forester, Outback, XV'
    ],
    [
        'name' => 'Isuzu',
        'description' => 'Tough commercial & pickup vehicles – D-Max, MU-X. Excellent for heavy-duty work & reliability.',
        'logo' => 'https://www.carlogos.org/car-brands-logos/isuzu-logo-2000x1400.png',
        'image' => 'https://www.isuzu.co.jp/world/product/pickup/dmax/img/main_pc.jpg',
        'popular_models' => 'D-Max, MU-X'
    ],
    [
        'name' => 'Honda',
        'description' => 'Smooth, fuel-efficient & reliable – CR-V, Fit, Civic, Accord. Great everyday & family options.',
        'logo' => 'https://www.carlogos.org/car-brands-logos/honda-logo-2000x1400.png',
        'image' => 'https://www.honda.com/-/media/Honda-Automobiles/Models/CR-V/2025/cr-v-main-gallery-1.jpg',
        'popular_models' => 'CR-V, Fit, Civic'
    ],
    // Add more brands easily...
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Car Brands We Serve - Ndagire Motor Company</title>
  <style>
    :root {
      --primary: #4f5252;    
      --dark: #1a1a1a;
      --light: #565758;
      --gray: #6c757d;
    }

    * { margin:0; padding:0; box-sizing:border-box; }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: var(--light);
      color: var(--dark);
      line-height: 1.6;
    }

    header {
      background: linear-gradient(135deg, var(--primary), #727675);
      color: white;
      text-align: center;
      padding: 4.5rem 1rem;
    }

    header h1 { font-size: 3.2rem; margin-bottom: 0.6rem; }
    header p  { font-size: 1.35rem; opacity: 0.95; }

    .container {
      max-width: 1240px;
      margin: 0 auto;
      padding: 0 1.5rem;
    }

    .brands-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
      gap: 2.5rem;
      padding: 4.5rem 0;
    }

    .brand-card {
      background: white;
      border-radius: 14px;
      overflow: hidden;
      box-shadow: 0 10px 28px rgba(0,0,0,0.15);
      transition: all 0.35s ease;
    }

    .brand-card:hover {
      transform: translateY(-14px);
      box-shadow: 0 20px 45px rgba(0,0,0,0.22);
    }

    .brand-logo {
      width: 140px;
      height: auto;
      margin: 1.8rem auto 1rem;
      display: block;
    }

    .brand-hero {
      width: 100%;
      height: 220px;
      object-fit: cover;
    }

    .brand-content {
      padding: 2rem 1.8rem;
      text-align: center;
    }

    .brand-content h3 {
      color: var(--primary);
      margin-bottom: 1rem;
      font-size: 1.85rem;
    }

    .brand-content p {
      color: var(--gray);
      margin-bottom: 1.3rem;
      font-size: 1.05rem;
    }

    .models {
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 1.5rem;
      display: block;
      font-size: 1.1rem;
    }

    .btn {
      display: inline-block;
      background: var(--primary);
      color: white;
      padding: 0.9rem 2.2rem;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: background 0.3s;
    }

    .btn:hover { background: #505353; }

    footer {
      background: var(--dark);
      color: white;
      text-align: center;
      padding: 3rem 1rem;
      margin-top: 5rem;
    }

    @media (max-width: 768px) {
      header h1 { font-size: 2.6rem; }
      .brands-grid { gap: 2rem; }
    }
  </style>
</head>
<body>
    <nav>
            <div class="ms-auto">
                <span class="light green me-3">Welcome My Dear, <?= htmlspecialchars($user['full_name']) ?>!</span>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
                <a href="services.php" class="btn btn-outline-light">Services</a>
                <a href="brands.php" class="btn btn-outline-light">Brands</a>
                <a href="gallery.php" class="btn btn-outline-light">Photos</a>
            </div>
            </div>
        </div>
        </nav>
     </div>

  <header>
    <div class="container">
      <h1>Car Brands We Specialize In</h1>
      <p>Expert service, repairs & maintenance for Uganda's most popular vehicles</p>
    </div>
  </header>

  <div class="container">
    <section class="brands-grid">
      <?php foreach ($brands as $brand): ?>
        <div class="brand-card">
          <img src="<?= htmlspecialchars($brand['image']) ?>" 
               alt="<?= htmlspecialchars($brand['name']) ?> vehicle" 
               class="brand-hero" loading="lazy">
          <img src="<?= htmlspecialchars($brand['logo']) ?>" 
               alt="<?= htmlspecialchars($brand['name']) ?> logo" 
               class="brand-logo">
          <div class="brand-content">
            <h3><?= htmlspecialchars($brand['name']) ?></h3>
            <p><?= htmlspecialchars($brand['description']) ?></p>
            <span class="models">Popular Models: <?= htmlspecialchars($brand['popular_models']) ?></span>
            <a href="tel:+256701234567" class="btn">Contact Us for <?= htmlspecialchars($brand['name']) ?></a>
          </div>
        </div>
      <?php endforeach; ?>
    </section>
  </div>

  <footer>
    <p>© <?= date("Y") ?> Yeahold Motor Company • Kampala, Uganda</p>
    <p>Call/WhatsApp: +256 701 234 567 • Professional Service for Japanese & Other Imported Cars</p>
    <p>Mon–Sat: 8:00 AM – 6:00 PM</p>
  </footer>

</body>
</html>