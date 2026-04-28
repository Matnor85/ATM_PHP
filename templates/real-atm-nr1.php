<!doctype html>
<html lang="sv">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../public/assets/css/main.css" />
    <link rel="preload" as="image" href="../public/assets/img/ATM2.jpg">
    <script src="../public/assets/js/real-atm.js" defer></script>
    <title>ATM</title>
  </head>
  <body>
    <div class="wrapper">
      <a href="home.php" class="back-button">Tillbaka</a>

      <main>
        <section class="atm-layout">
          <div class="real-atm-image">
            <img src="../public/assets/img/ATM2.jpg" class="real-atm-img" alt="ATM bild" />

                <!-- Bankomat spegel -->
                <?php require 'camera.php'; ?>
                <!-- Bankomat skärm -->
                <?php require __DIR__ . '/shared/atm_screen.php'; ?>
                <!-- Kortanimation -->
                <?php require __DIR__ . '/shared/atm_cardinformation.php'; ?>     
                <!-- KORTLÄSARE -->
                <?php require __DIR__ . '/shared/atm_card.php'; ?>
                <!-- VÄNSTER SIDOKNAPPAR -->
                <?php require __DIR__ . '/shared/atm_side_buttons.php'; ?>
                <!-- SIFFERKNAPPAR -->
                <?php require __DIR__ . '/shared/atm_num_buttons.php'; ?>
                <!-- FUNKTIONSKNAPPAR -->
                <?php require __DIR__ . '/shared/atm_fn_buttons.php'; ?>
             
          </div>
        </section>
      </main>
    </div>
  </body>
</html>
