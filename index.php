<?php
session_start();
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/auth.php";

$category = $_GET['category'] ?? null; // categorie filtrată

$products = [
    ['id'=>1,'name'=>'Set Lenjerie 1','price'=>120,'category'=>'seturi','img'=>'assets/images/set1.jpg'],
    ['id'=>2,'name'=>'Set Lenjerie 2','price'=>130,'category'=>'seturi','img'=>'assets/images/set2.jpg'],
    ['id'=>3,'name'=>'Sutien 1','price'=>70,'category'=>'sutiene','img'=>'assets/images/sutien1.jpg'],
    ['id'=>4,'name'=>'Chilot 1','price'=>50,'category'=>'chiloti','img'=>'assets/images/chilot1.jpg'],
    ['id'=>5,'name'=>'Body 1','price'=>90,'category'=>'body','img'=>'assets/images/body1.jpg'],
];

$filtered = [];
if($category){
    foreach($products as $p){
        if($p['category']===$category) $filtered[]=$p;
    }
}else{
    $filtered = $products;
}

$user = getLoggedInUser();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>MFK Lingerie</title>
<link rel="icon" href="assets/images/logo.jpeg" type="image/x-icon" />
<link rel="stylesheet" href="assets/css/style.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/main.js" defer></script>
  <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
<script>
  window.OneSignalDeferred = window.OneSignalDeferred || [];
  OneSignalDeferred.push(async function(OneSignal) {
    await OneSignal.init({
      appId: "2cd60467-29d9-420c-ad7a-04faeb5c3225",
      allowLocalhostAsSecureOrigin: true,
    });
  });
</script>

</head>
<body>

<header>
    <div class="logo"><a href="index.php"><img src="assets/images/logo.jpeg" alt="Logo"></a></div>
    <div class="search-container">
            <input type="text" id="searchInput" placeholder="Caută produse...">
            <button id="searchBtn">🔍</button>
        </div>

    <nav>
        <a href="index.php" class="<?= $category===null?'active':'' ?>">Home</a>
        <a href="index.php?category=seturi" class="<?= $category==='seturi'?'active':'' ?>">Seturi</a>
        <a href="index.php?category=sutiene" class="<?= $category==='sutiene'?'active':'' ?>">Sutiene</a>
        <a href="index.php?category=chiloti" class="<?= $category==='chiloti'?'active':'' ?>">Chiloți</a>
        <a href="index.php?category=body" class="<?= $category==='body'?'active':'' ?>">Body-uri</a>
    </nav>

    <div class="right-area">
        <?php if($user): ?>
            <span>Bine ai venit, <?= htmlspecialchars($user['username']) ?></span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <button id="loginBtn">Login / Register</button>
        <?php endif; ?>
        <button id="cartBtn">Coș (<span id="cartCount">0</span>)</button>
    </div>
</header>

<main>
    <?php if($category !== null): ?>
        <div id="product-list">
            <?php foreach($filtered as $p): ?>
                <div class="product">
                    <img src="<?= $p['img'] ?>" alt="<?= $p['name'] ?>">
                    <h4><?= $p['name'] ?></h4>
                    <p><?= $p['price'] ?> lei</p>
                    <button class="add-to-cart" data-id="<?= $p['id'] ?>">Adaugă în coș</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- CAROUSEL -->
    
<div class="hero-carousel">

    <div class="hero-slide active">
    <video class="hero-video" autoplay muted loop playsinline>
        <source src="assets/videos/video.mp4" type="video/mp4">
    </video>
    <div class="hero-text">
        <h2>Eleganță redefinită</h2>
        <p>Descoperă colecția noastră premium</p>
        <a href="index.php?category=seturi" class="hero-btn">Explorați acum</a>
    </div>
</div>

    <div class="hero-slide">
        <img src="assets/images/velvetgreenset.jpg" alt="">
        <div class="hero-text">
            <h2>Fii irezistibilă</h2>
            <p>Stil, confort și încredere</p>
            <a href="index.php?category=seturi" class="hero-btn">Seturi</a>
        </div>
    </div>

    <div class="hero-slide">
        <img src="assets/images/lacepanties.jpg" alt="">
        <div class="hero-text">
            <h2>Forme perfecte</h2>
            <p>Modele create pentru tine</p>
            <a href="index.php?category=chiloti" class="hero-btn">Chiloți</a>
        </div>
    </div>

    <div class="hero-dots"></div>
</div>
    <?php endif; ?>



</main>

<!-- LOGIN / REGISTER MODAL -->
<div id="loginModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Login</h2>
        <input type="email" id="signin-email" placeholder="Email">
        <input type="password" id="signin-password" placeholder="Parolă">
        <button id="signinBtn">Sign In</button>
        <h3>Creare cont</h3>
        <input type="text" id="signup-name" placeholder="Nume">
        <input type="email" id="signup-email" placeholder="Email">
        <input type="password" id="signup-password" placeholder="Parolă">
        <button id="signupBtn">Create Account</button>
    </div>
</div>

<!-- CART MODAL -->
<div id="cartModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="closeCart">&times;</span>
        <h2>Coșul tău</h2>
        <div id="cartItems"></div>
        <p>Total: <span id="cartTotal"></span> lei</p>
        <button id="checkoutBtn">Finalizare comandă</button>
    </div>
</div>

<style>
/* Simplu styling modal */
.modal{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);}
.modal-content{background:#fff;margin:10% auto;padding:20px;width:300px;position:relative;}
.close,.closeCart{position:absolute;top:10px;right:10px;cursor:pointer;}
.cart-item{margin-bottom:10px;}
</style>
<footer class="mfk-footer">
    <div class="mfk-container">

        <!-- Col 1 -->
        <div class="mfk-col">
            <h3>MFK Lingerie</h3>
            <p>Descoperă colecția noastră premium de lenjerie intimă, creată pentru eleganță și confort.</p>
        </div>

        <!-- Col 2 -->
        <div class="mfk-col">
            <h4>Linkuri utile</h4>
            <ul>
                <li><a href="#">Despre noi</a></li>
                <li><a href="#">Politica de retur</a></li>
                <li><a href="#">Termeni și condiții</a></li>
            </ul>
        </div>

        <!-- Col 3 -->
        <div class="mfk-col">
            <h4>Contact</h4>
            <p><strong>Telefon:</strong> <a href="tel:+40775276402">+40 775 276 402</a></p>
            <p><strong>Email:</strong> <a href="mailto:mfklingerie@gmail.com">mfklingerie@gmail.com</a></p>

            <div class="mfk-social">
                <a href="https://www.instagram.com/mfklingerie?igsh=aGs5bzMyaGZjOWxz" target="_blank">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/9/95/Instagram_logo_2022.svg" alt="Instagram">
                </a>
                <a href="https://www.tiktok.com/@mfklingerie1" target="_blank">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/3/34/Ionicons_logo-tiktok.svg" alt="TikTok">

                </a>
            </div>
        </div>

    </div>

    <div class="mfk-bottom">
        © 2025 MFK Lingerie – Toate drepturile rezervate.
    </div>
</footer>

</body>
</html>
