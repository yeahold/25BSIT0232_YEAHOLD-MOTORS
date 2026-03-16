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

// Services array – easy to edit or expand
$services = [
    [
        'title' => 'Oil Change & Filter Replacement',
        'description' => 'Regular oil changes keep your engine running smoothly and extend its life. We use high-quality oils and filters.',
        'image' => 'https://airtasker-seo-assets-prod.s3.amazonaws.com/en_AU/1626143444896_oil-and-filter-change-hero.jpg',
        'price' => 'From UGX 80,000'
    ],
    [
        'title' => 'Brake Inspection & Repair',
        'description' => 'Safety first! We inspect pads, discs, calipers, fluid, and lines — full brake repair & replacement available.',
        'image' => 'https://autoservicefairfax.com/wp-content/uploads/2025/11/Brakes-1024x768.jpg',
        'price' => 'From UGX 150,000'
    ],
    [
        'title' => 'Tire Rotation & Balancing',
        'description' => 'Even tire wear improves handling, fuel efficiency, and extends tire life. Includes balancing & alignment check.',
        'image' => 'https://s7d1.scene7.com/is/image/bridgestone/fcac-blog-alignment-2019-blog-images-2019-09-fcac-web-bsro?scl=1',
        'price' => 'UGX 50,000 – 90,000'
    ],
    [
        'title' => 'Engine Diagnostics & Tune-up',
        'description' => 'Fast computer diagnostics using modern scan tools + spark plugs, filters, belts, and full tune-up service.',
        'image' => 'https://repairsmith-prod-wordpress.s3.amazonaws.com/2021/04/iStock-175253106-1.jpg',
        'price' => 'From UGX 120,000'
    ],
    [
        'title' => 'AC Repair & Gas Refill',
        'description' => 'Stay cool in any weather! We diagnose leaks, repair compressors, and recharge your AC system professionally.',
        'image' => 'https://crawfordsautoservice.com/wp-content/uploads/2020/03/air-conditioning-service-ac-recharge.png',
        'price' => 'From UGX 100,000'
    ],
    [
        'title' => 'Suspension & Steering Service',
        'description' => 'Shocks, struts, ball joints, bushings, wheel alignment — for a smoother, safer ride and better control.',
        'image' => 'https://www.westhighservice.com/wp-content/uploads/2022/11/wheel-alighnment-darien-ct-1080x675.jpg',
        'price' => 'From UGX 200,000'
    ],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Our Services - Yeahold Motor Company</title>
  <style>
    :root {otors
      --primary: #2d2f2f;     
      --dark: #1a1a1a;
      --light: #5d5f61;
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
      background: linear-gradient(135deg, var(--primary), #11b4ac);
      color: white;
      text-align: center;
      padding: 4rem 1rem;
    }

    header h1 { font-size: 3rem; margin-bottom: 0.6rem; }
    header p  { font-size: 1.3rem; opacity: 0.95; }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 1.5rem;
      background: #393a39;
    }

    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
      gap: 2.2rem;
      padding: 4rem 0;
    }

    .service-card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 8px 24px rgba(0,0,0,0.14);
      transition: all 0.32s ease;
    }

    .service-card:hover {
      transform: translateY(-12px);
      box-shadow: 0 18px 40px rgba(0,0,0,0.2);
    }

    .service-img {
      width: 100%;
      height: 240px;
      object-fit: cover;
      object-position: center;
    }

    .service-content {
      padding: 2rem 1.8rem;
    }

    .service-content h3 {
      color: var(--primary);
      margin-bottom: 1rem;
      font-size: 1.55rem;
    }

    .service-content p {
      color: var(--gray);
      margin-bottom: 1.2rem;
    }

    .price {
      font-weight: 700;
      color: #2c3e50;
      font-size: 1.25rem;
      display: block;
      margin-bottom: 1.4rem;
    }

    .btn {
      display: inline-block;
      background: var(--primary);
      color: white;
      padding: 0.85rem 2rem;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: background 0.3s;
    }

    .btn:hover { background: #6cd6e3; }

    footer {
      background: var(--dark);
      color: white;
      text-align: center;
      padding: 2.5rem 1rem;
      margin-top: 4rem;
    }

    @media (max-width: 700px) {
      header h1 { font-size: 2.4rem; }
      .services-grid { gap: 1.8rem; }
    }
  </style>
</head>
<body>
 <nav>
            <div class="ms-auto">
                <span class="light green me-3"><b>Welcome My Dear, <?= htmlspecialchars($user['full_name']) ?>!</span>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
                <a href="services.php" class="btn btn-outline-light">Services</a>
                 <a href="brands.php" class="btn btn-outline-light">Brands</a>
                 <a href="gallery.php" class="btn btn-outline-light">Photos</a>

            </div>
        </div>
        </nav>
     </div>
  <header>
    <div class="container">
      <h1>Our Professional Auto Services</h1>
      <p>Trusted vehicle repair & maintenance right here in Kampala – Quality you can rely on</p>
    </div>
  </header>

  <div class="container">
    <section class="services-grid">
      <?php foreach ($services as $service): ?>
        <div class="service-card">
          <img src="<?= htmlspecialchars($service['image']) ?>" 
               alt="<?= htmlspecialchars($service['title']) ?> at Ndagire Motor Company" 
               class="service-img" loading="lazy">
          <div class="service-content">
            <h3><?= htmlspecialchars($service['title']) ?></h3>
            <p><?= htmlspecialchars($service['description']) ?></p>
            <span class="price"><?= htmlspecialchars($service['price']) ?></span>
            <a href="tel:+256791463105" class="btn">Book Appointment</a>
          </div>
        </div>
      <?php endforeach; ?>
    </section>
  </div>

  <footer>
    <p>© <?= date("Y") ?> Yeahold Motor Company • Kampala, Uganda</p>
    <p>Contact: +256 701 234 567 • WhatsApp: +256 791463105</p>
    <p>Mon–Sat: 8:00 AM – 6:00 PM | Emergency Service Available</p>
  </footer>

</body>
</html>