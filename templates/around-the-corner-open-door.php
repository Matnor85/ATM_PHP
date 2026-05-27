<!DOCTYPE html>
<html lang="sv">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="assets/css/main.css">
        <title>My ATM – Öppen dörr</title>
    </head>

    <body>
        <main>
            <div class="image-container">
                
                <a href="index.php?page=around-the-corner-closed-door" class="back-button" title="Stäng dörren">&#8592;</a>
                
                <img src="assets/img/around-corner.png" class="atm-image" id="atm-bg" alt="Bankomat med öppen dörr"/>

                <canvas id="matrix" class="matrix-canvas"></canvas>
                <div id="server-overlay" class="atm-hotspot"></div>

                <a href="index.php?page=admin_login" class="atm-hotspot" style="left:13.5%; top:43.6%; width:3.9%; height:6%;" title="Admin-dator"></a>

                <form method="POST" action="index.php" style="position: absolute; left: 8.8%; top: 30%; width: 4%; height: 31.5%; z-index: 50;">
                    <input type="hidden" name="action" value="install_database">
                    <button type="submit" style="width: 100%; height: 100%; background: transparent; border: none; cursor: pointer; outline: none;" title="Återställ och seeda databas"></button>
                </form>

                <script src="assets/js/matrix.js"></script>
            </div>
        </main>
    </body>
</html>