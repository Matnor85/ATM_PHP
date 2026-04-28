<!doctype html>
<html lang="sv">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bankomat</title>
    <link rel="stylesheet" href="../public/assets/css/style.css" />
  </head>
  <body>
    <div class="wood-wall"></div>
    <div class="floor"></div>

    <!-- Tillbaka-knapp -->
    <a href="home.php" class="back-button">Tillbaka</a>

    <div class="bankomat-sign">Bankomat<sup>®</sup></div>

    <div class="atm-outer-frame">
      <div class="atm-frame-header">
        <span class="satt-in">Sätt in</span>
        <div class="divider"></div>
        <span class="ta-ut">Ta ut</span>
      </div>

      <div class="atm-frame-mid">
        <div class="lock-badge"></div>
      </div>

      <div class="atm-machine">
        <div style="display: flex; gap: 8px; align-items: stretch">
          <div class="side-keys">
            <div class="side-key"></div>
            <div class="side-key"></div>
            <div class="side-key"></div>
            <div class="side-key"></div>
          </div>

          <div class="screen-wrapper" style="flex: 1">
            <div class="screen">
              <div class="screen-menu">
                <div class="menu-item left">
                  <div class="menu-arrow"></div>
                  <div>
                    <div class="menu-label">Kontoinformation</div>
                  </div>
                </div>
                <div class="menu-item right">
                  <div class="menu-label">Saldo</div>
                  <div class="menu-arrow"></div>
                </div>
                <div class="menu-item left">
                  <div class="menu-arrow"></div>
                  <div>
                    <div class="menu-label">Fler tjänster</div>
                  </div>
                </div>
                <div class="menu-item right">
                  <div class="menu-label">Uttag</div>
                  <div class="menu-arrow"></div>
                </div>
                <div class="menu-item left">
                  <div class="menu-arrow"></div>
                  <div>
                    <div class="menu-label">Insättning</div>
                  </div>
                </div>
                <div class="menu-item right">
                  <div class="menu-label">
                    Snabbuttag <span style="color: #00d4ff">500 kr</span>
                  </div>
                  <div class="menu-arrow"></div>
                </div>
                <div class="menu-item left">
                  <div class="menu-arrow"></div>
                  <div>
                    <div class="menu-label">PIN-byte</div>
                  </div>
                </div>
                <div class="menu-item right">
                  <div class="menu-label">Betalning</div>
                  <div class="menu-arrow"></div>
                </div>
              </div>
              <div class="screen-title">Välj en transaktion</div>
            </div>
          </div>

          <div
            style="
              display: flex;
              flex-direction: column;
              gap: 8px;
              align-items: flex-end;
            "
          >
            <div class="side-keys right-side">
              <div class="side-key"></div>
              <div class="side-key"></div>
              <div class="side-key"></div>
              <div class="side-key"></div>
            </div>

            <div class="card-reader">
              <div class="card-reader-light"></div>
              <div class="card-slot"></div>
            </div>
          </div>
        </div>

        <div class="receipt-area">
          <div class="receipt-slot"></div>
        </div>

        <div class="bottom-slots">
          <div class="bill-slot">
            <div class="bill-slot-inner"></div>
          </div>
          <div class="cash-icon">SEK</div>
          <div class="bill-slot" style="max-width: 70px">
            <div class="bill-slot-inner"></div>
          </div>
        </div>

        <div class="keypad-section">
          <div class="keypad">
            <div class="key">1</div>
            <div class="key">2</div>
            <div class="key">3</div>
            <div class="key">4</div>
            <div class="key">5</div>
            <div class="key">6</div>
            <div class="key">7</div>
            <div class="key">8</div>
            <div class="key">9</div>
            <div class="key cancel">Avbryt</div>
            <div class="key">0</div>
            <div class="key confirm">OK</div>
          </div>
        </div>
      </div>
      <div class="atm-frame-bottom"></div>
    </div>
  </body>
</html>
