<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../public/assets/css/main.css">
        <link rel="preload" as="image" href="../public/assets/img/ATM2.jpg">
        <title>My ATM</title>
    </head>
   
    <body>
         <!-- Svart overlay för fade-ut -->
        <div class="page-overlay" id="overlay"></div>
 
        <main>
            <div class="image-container">
                <img src="../public/assets/img/Trädpanel.jpg" class="atm-image" id="atm-bg" alt="Bankomater" />
                
                <!-- Klickbara områden i procent av bildens storlek -->
                <a href="real-atm-nr1.php" class="atm-hotspot" style="left:30%;top:35%;width:11.5%;height:43%;" title="Bankomat 1"></a>
                <a href="real-atm.php" class="atm-hotspot" style="left:45.5%;top:38.5%;width:9.3%;height:37.5%;" title="Bankomat 2"></a>
                <a href="atm-page-digital.php" class="atm-hotspot" style="left:60.4%;top:41.5%;width:6.2%;height:31%;" title="Bankomat 3"></a>
            </div>
        </main>
        <script src="../public/assets/js/atm-overview.js"></script>
    </body>
</html>