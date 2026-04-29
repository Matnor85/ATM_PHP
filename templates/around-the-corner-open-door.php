<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../public/assets/css/main.css">
        <script src="../public/assets/js/matrix.js" defer></script>
        
        <title>My ATM</title>
    </head>
   
    <body>
         <!-- Svart overlay för fade-ut -->
        <!-- <div class="page-overlay" id="overlay"></div> -->
 
        <main>
            <div class="image-container">
                <img src="../public/assets/img/around-corner.png" class="atm-image" id="atm-bg" alt="Bankomater" />
                <!-- Matrix-animation -->
                <canvas id="matrix" class="matrix-canvas"></canvas>
                 <!-- Ytan för server-lamporna -->
                 <div id="server-overlay" class="atm-hotspot"></div>
            </div>
                <!-- Klickbara områden i procent av bildens storlek -->
                <a href="around-the-corner-closed-door.php" class="atm-hotspot" style="left:5.3%;top:42.2%;width:2%;height:6%;" title="Kort_läsare"></a>
                <a href="real-atm-nr1.php" class="atm-hotspot" style="left:13.5%;top:43.6%;width:3.9%;height:6%;" title="Admin-dator"></a>
                <!-- <a href="real-atm.php" class="atm-hotspot" style="left:45.5%;top:38.5%;width:9.3%;height:37.5%;" title="Bankomat 2"></a>
                <a href="atm-page-digital.php" class="atm-hotspot" style="left:60.4%;top:41.5%;width:6.2%;height:31%;" title="Bankomat 3"></a> -->
                <!-- Vidare till Adminpanelen -->
            </div>
        </main>
        <!-- <script src="../public/assets/js/atm-overview.js"></script> -->
    </body>
</html>