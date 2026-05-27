<!doctype html>
<html lang="sv">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="assets/css/main.css" />
        <link rel="stylesheet" href="assets/css/atm.css" />
        <link rel="preload" as="image" href="assets/img/ATM2.jpg">
        <title>ATM</title>
    </head>
    <body>
        <div class="wrapper">

            <main>
                <div id="php-csrf-carrier" data-token="<?php echo $_SESSION['csrf_token'] ?? ''; ?>" style="display:none;"></div>

                <section class="atm-layout">
                    <div class="real-atm-image">
                        <img src="assets/img/ATM2.jpg" class="real-atm-img" alt="ATM bild" />

                        <?php require __DIR__ . '/camera.php'; ?>
                        <?php require __DIR__ . '/../shared/atm_screen.php'; ?>
                        <?php require __DIR__ . '/../shared/atm_cardinformation.php'; ?>
                        <?php require __DIR__ . '/../shared/atm_side_buttons.php'; ?>
                        <?php require __DIR__ . '/../shared/atm_num_buttons.php'; ?>
                        <?php require __DIR__ . '/../shared/atm_fn_buttons.php'; ?>
                    </div>

                    <div class="external-card-panel">
                        <h3>Välj ett bankkort:</h3>
                        <?php require __DIR__ . '/../shared/atm_card.php'; ?>
                    </div>
                    <div class="real-atm-image">
                          <a href="index.php?page=home" class="back-button" title="Tillbaka till huvudsidan">
                              Tillbaka
                          </a>
                    </div>
                </section>
            </main>
        </div>

        <script src="assets/js/real-atm.js"></script>
        <script src="assets/js/camera.js"></script>
    </body>
</html>