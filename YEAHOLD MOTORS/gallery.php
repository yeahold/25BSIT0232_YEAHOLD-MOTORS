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
// Array of featured cars – popular in Uganda market 2025/2026
$cars = [
    [
        'model' => 'Toyota Noah',
        'year' => '2018–2022',
        'description' => 'Spacious 7–8 seater family van. Very popular for taxi/business use in Kampala. Reliable, fuel-efficient.',
        'price' => 'UGX 45M – 75M',
        'image' => 'https://img.autotrader.co.jp/car-top/resize_width/640/car-top/images/article/2023/07/31/20230731_1_1.jpg', // clean Noah example
    ],
    [
        'model' => 'Toyota Hilux',
        'year' => '2016–2023',
        'description' => 'Tough double-cab pickup. King of Uganda roads – great for business, farming & rough terrain.',
        'price' => 'UGX 55M – 120M',
        'image' => 'https://www.toyota.co.ug/-/media/toyota-uganda/vehicles/hilux/gallery/hilux-gallery-1.jpg?h=500&iar=0&w=800&mw=800', // official-ish
    ],
    [
        'model' => 'Toyota Land Cruiser Prado',
        'year' => '2014–2021',
        'description' => 'Premium SUV – durable, off-road capable, high resale value. Favorite for families & executives.',
        'price' => 'UGX 90M – 180M',
        'image' => 'https://www.netcarshow.com/Toyota-Land_Cruiser_Prado-2020-1600x1200.jpg',
    ],
    [
        'model' => 'Toyota Harrier',
        'year' => '2014–2020',
        'description' => 'Luxury crossover SUV. Stylish, comfortable, smooth drive – very sought after in Uganda.',
        'price' => 'UGX 50M – 95M',
        'image' => 'https://www.netcarshow.com/Toyota-Harrier-2020-1600x1200.jpg',
    ],
    [
        'model' => 'Toyota Corolla',
        'year' => '2016–2023',
        'description' => 'Reliable sedan – low maintenance, fuel saver. Perfect daily driver for city use.',
        'price' => 'UGX 35M – 65M',
        'image' => 'https://www.netcarshow.com/Toyota-Corolla-2023-1600x1200.jpg',
    ],
    [
        'model' => 'Nissan Navara',
        'year' => '2015–2022',
        'description' => 'Strong pickup alternative to Hilux. Good payload & off-road ability for work.',
        'price' => 'UGX 50M – 90M',
        'image' => 'https://www.netcarshow.com/Nissan-Navara-2022-1600x1200.jpg',
    ],
    [
        'model' => 'Mitsubishi Delica',
        'year' => '2013–2020',
        'description' => 'Rugged 7–8 seater MPV. Excellent for families & group transport on rough roads.',
        'price' => 'UGX 40M – 70M',
        'image' => 'https://img.autotrader.co.jp/car-top/resize_width/640/car-top/images/article/2024/05/20/20240520_1_1.jpg',
    ],
    [
        'model' => 'Subaru Forester',
        'year' => '2016–2022',
        'description' => 'AWD safety & performance. Great handling in rain & on bad roads.',
        'price' => 'UGX 45M – 85M',
        'image' => 'https://www.subaru.com/content/dam/subaru/vehicles/2025/forester/gallery/exterior/2025-subaru-forester-front-three-quarter.jpg',
    ],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Car Gallery - Yeahold Motor Company</title>
  <style>
    :root {
      --primary: #4a4c4c;   
      --dark: #1a1a1a;
      --light: #a5a6a8;
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
      background: linear-gradient(135deg, var(--primary), #383a3a);
      color: white;
      text-align: center;
      padding: 5rem 1rem;
    }

    header h1 { font-size: 3.4rem; margin-bottom: 0.7rem; }
    header p  { font-size: 1.4rem; opacity: 0.95; max-width: 800px; margin: 0 auto; }

    .container {
      max-width: 1280px;
      margin: 0 auto;
      padding: 0 1.5rem;
    }

    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
      gap: 2.8rem;
      padding: 5rem 0;
    }

    .car-card {
      background: white;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 12px 32px rgba(0,0,0,0.16);
      transition: all 0.4s ease;
    }

    .car-card:hover {
      transform: translateY(-16px);
      box-shadow: 0 24px 50px rgba(0,0,0,0.24);
    }

    .car-img {
      width: 100%;
      height: 260px;
      object-fit: cover;
      object-position: center;
    }

    .car-content {
      padding: 2.2rem 2rem;
      text-align: center;
    }

    .car-content h3 {
      color: var(--primary);
      margin-bottom: 1rem;
      font-size: 2rem;
    }

    .car-content p {
      color: var(--gray);
      margin-bottom: 1.4rem;
      font-size: 1.1rem;
    }

    .price {
      font-weight: 700;
      color: #2c3e50;
      font-size: 1.4rem;
      margin-bottom: 1.6rem;
      display: block;
    }

    .btn {
      display: inline-block;
      background: var(--primary);
      color: white;
      padding: 1rem 2.4rem;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      font-size: 1.1rem;
      transition: background 0.3s;
    }

    .btn:hover { background: #16c9cc; }

    footer {
      background: var(--dark);
      color: white;
      text-align: center;
      padding: 3.5rem 1rem;
      margin-top: 5rem;
    }

    @media (max-width: 800px) {
      header h1 { font-size: 2.8rem; }
      .gallery-grid { gap: 2rem; }
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

  <header>
    <div class="container">
      <h1>Our Featured Cars Gallery</h1>
      <p>Popular Japanese imports & reliable vehicles we service, repair & sell in Kampala – Quality you trust</p>
    </div>
  </header>

  <div class="container">
    <section class="gallery-grid">
      <?php foreach ($cars as $car): ?>
        <div class="car-card">
          <img src="<?= htmlspecialchars($car['image']) ?>" 
               alt="<?= htmlspecialchars($car['model']) ?> at Ndagire Motor Company" 
               class="car-img" loading="lazy">
          <div class="car-content">
            <h3><?= htmlspecialchars($car['model']) ?></h3>
            <p><?= htmlspecialchars($car['description']) ?></p>
            <span class="price"><?= htmlspecialchars($car['price']) ?></span>
            <a href="tel:+256701234567" class="btn">Inquire about <?= htmlspecialchars($car['model']) ?></a>
          </div>
        </div>
      <?php endforeach; ?>
    </section>
  </div>

  <footer>
    <p>© <?= date("Y") ?> Yeahold Motor Company • Kampala, Uganda</p>
    <p>Call/WhatsApp: +256 701 234 567 • Expert Service for Popular Japanese & Imported Cars</p>
    <p>Mon–Sat: 8:00 AM – 6:00 PM | Viewing & Inspections Welcome</p>
  </footer>

</body>
</html>