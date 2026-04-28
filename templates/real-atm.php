<!doctype html>
<html lang="sv">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../public/assets/css/main.css" />
    <link rel="preload" as="image" href="../public/assets/img/ATM2.jpg">
    <title>ATM</title>
  </head>
  <body>
    <div class="wrapper">
      <a href="home.php" class="back-button">Tillbaka</a>

      <main>
        <section class="atm-layout">
          <div class="real-atm-image">
            <img src="../public/assets/img/ATM2.jpg" class="real-atm-img" alt="ATM bild" />
            
            <!-- Virtuell skärm – positioneras ovanpå bilden -->
            <div class="virtual-screen" id="screen">
              <div class="screen-left" id="screen-left"></div>
              <div class="screen-mid" id="screen-mid"></div>
              <div class="screen-right" id="screen-right"></div>
            </div>

            <!-- Kortanimation -->
            <div class="card-anim" id="card-anim">
              <div class="card" id="card"></div>
            </div>

            <!-- KORTLÄSARE -->
            <div
              class="btn-hotspot grp-card"
              id="btn-kort"
              style="left: 70.5%; top: 43.4%; width: 15.3%; height: 9.3%"
              title="Kortläsare"
            ></div>

            <!-- VÄNSTER SIDOKNAPPAR -->
            <div
              class="btn-hotspot grp-side"
              id="btn-v1"
              style="left: 15.7%; top: 47.4%; width: 3%; height: 2.8%"
              title="Vänster 1"
            ></div>
            <div
              class="btn-hotspot grp-side"
              id="btn-v2"
              style="left: 15.7%; top: 51%; width: 3%; height: 2.8%"
              title="Vänster 2"
            ></div>
            <div
              class="btn-hotspot grp-side"
              id="btn-v3"
              style="left: 15.7%; top: 55%; width: 3%; height: 2.6%"
              title="Vänster 3"
            ></div>
            <div
              class="btn-hotspot grp-side"
              id="btn-v4"
              style="left: 15.7%; top: 58.4%; width: 3%; height: 2.8%"
              title="Vänster 4"
            ></div>

            <!-- HÖGER SIDOKNAPPAR -->
            <div
              class="btn-hotspot grp-side"
              id="btn-h1"
              style="left: 60%; top: 47.4%; width: 3%; height: 2.8%"
              title="Höger 1"
            ></div>
            <div
              class="btn-hotspot grp-side"
              id="btn-h2"
              style="left: 60%; top: 51%; width: 3%; height: 2.8%"
              title="Höger 2"
            ></div>
            <div
              class="btn-hotspot grp-side"
              id="btn-h3"
              style="left: 60%; top: 55%; width: 3%; height: 2.6%"
              title="Höger 3"
            ></div>
            <div
              class="btn-hotspot grp-side"
              id="btn-h4"
              style="left: 60%; top: 58.4%; width: 3%; height: 2.8%"
              title="Höger 4"
            ></div>

            <!-- SIFFERKNAPPAR -->
            <div
              class="btn-hotspot grp-num"
              id="btn-n1"
              style="left: 25.9%; top: 80%; width: 4.9%; height: 1.9%"
              title="1"
            ></div>
            <div
              class="btn-hotspot grp-num"
              id="btn-n2"
              style="left: 31%; top: 80%; width: 4.9%; height: 1.9%"
              title="2"
            ></div>
            <div
              class="btn-hotspot grp-num"
              id="btn-n3"
              style="left: 36.4%; top: 80%; width: 4.8%; height: 1.9%"
              title="3"
            ></div>
            <div
              class="btn-hotspot grp-num"
              id="btn-n4"
              style="left: 25.2%; top: 82.2%; width: 5%; height: 2.5%"
              title="4"
            ></div>
            <div
              class="btn-hotspot grp-num"
              id="btn-n5"
              style="left: 30.6%; top: 82.2%; width: 5%; height: 2.5%"
              title="5"
            ></div>
            <div
              class="btn-hotspot grp-num"
              id="btn-n6"
              style="left: 36%; top: 82.2%; width: 5%; height: 2.5%"
              title="6"
            ></div>
            <div
              class="btn-hotspot grp-num"
              id="btn-n7"
              style="left: 24.8%; top: 84.8%; width: 5%; height: 2.5%"
              title="7"
            ></div>
            <div
              class="btn-hotspot grp-num"
              id="btn-n8"
              style="left: 30.3%; top: 84.8%; width: 5%; height: 2.5%"
              title="8"
            ></div>
            <div
              class="btn-hotspot grp-num"
              id="btn-n9"
              style="left: 35.8%; top: 84.8%; width: 5%; height: 2.5%"
              title="9"
            ></div>
            <div
              class="btn-hotspot grp-num"
              id="btn-nstar"
              style="left: 24.4%; top: 87.4%; width: 5%; height: 2.6%"
              title="*"
            ></div>
            <div
              class="btn-hotspot grp-num"
              id="btn-n0"
              style="left: 29.8%; top: 87.4%; width: 5%; height: 2.6%"
              title="0"
            ></div>
            <div
              class="btn-hotspot grp-num"
              id="btn-n00"
              style="left: 35.4%; top: 87.4%; width: 5%; height: 2.6%"
              title="00"
            ></div>

            <!-- FUNKTIONSKNAPPAR -->
            <div
              class="btn-hotspot grp-fn"
              id="btn-avbryt"
              style="left: 43%; top: 80%; width: 9.4%; height: 1.9%"
              title="Avbryt"
            ></div>
            <div
              class="btn-hotspot grp-fn"
              id="btn-ratta"
              style="left: 43%; top: 82.2%; width: 9.6%; height: 2.5%"
              title="Rätta"
            ></div>
            <div
              class="btn-hotspot grp-fn"
              id="btn-ok"
              style="left: 42.6%; top: 87.4%; width: 10%; height: 2.6%"
              title="OK"
            ></div>
          </div>
        </section>
      </main>
    </div>
    <script src="../public/assets/js/real-atm.js"></script>
  </body>
</html>
