// ============================================
// REAL-ATM.JS – RENSAD OCH KOPPLAD TILL PHP-ROUTER
// ============================================

let kortInsatt = false;

// === SKÄRMELEMENT ===
const screenMid = document.getElementById("screen-mid");

// === HJÄLPFUNKTION: VISA GRUNDSKÄRMAR ===
function visaStatiskSkärm(htmlInnehåll) {
  if (screenMid) {
    screenMid.innerHTML = htmlInnehåll;
  }
}

// === TA UT KORT / NOLLSTÄLL ===
function taUtKort() {
  kortInsatt = false;
  const card = document.getElementById("card");

  visaStatiskSkärm(
    '<p class="screen-avbrutet" style="color: #ff6666; font-family: monospace; text-align: center; font-weight: bold;">Session avbruten</p><p class="screen-subtitle" style="color: white; font-family: monospace; text-align: center; opacity: 0.8; font-size: 0.8rem;">Ta ditt kort</p>',
  );

  if (card) {
    card.classList.remove("insatt");
    card.classList.add("uttag");
    setTimeout(() => {
      card.classList.remove("uttag");
    }, 1000);
  }

  setTimeout(() => {
    visaStatiskSkärm(
      '<p class="screen-idle" style="color: white; font-family: monospace; text-align: center; padding-top: 40px; margin: 0;">Välkommen<br><span style="font-size: 0.8rem; opacity: 0.7;">Sätt in ditt kort</span></p>',
    );
  }, 2000);
}

// === KORTLÄSARE (KLICK PÅ ATT SÄTTA IN KORT) ===
let valtKortnummer = "";

document.addEventListener("DOMContentLoaded", () => {
  initieraKortKlick();
});

// === DE FYSISKA SIFFERKNAPPARNA (0-9 och 00) ===
const sifferKnappar = document.querySelectorAll(".grp-num");
if (sifferKnappar && sifferKnappar.length > 0) {
  sifferKnappar.forEach((knapp) => {
    knapp.addEventListener("click", () => {
      if (!kortInsatt) return;

      const pinInput = document.getElementById("pin");

      if (pinInput) {
        const siffra = knapp.getAttribute("title");

        if (siffra && (!isNaN(siffra) || siffra === "00")) {
          const maxLängd = parseInt(pinInput.getAttribute("maxlength")) || 4;

          if (pinInput.value.length < maxLängd) {
            pinInput.value += siffra;
            if (pinInput.value.length > maxLängd) {
              pinInput.value = pinInput.value.slice(0, maxLängd);
            }
          }
        }
      }
    });
  });
}

// === GUL RÄTTA-KNAPP (BACKSPACE) ===
const btnRatta = document.getElementById("btn-ratta");
if (btnRatta) {
  btnRatta.addEventListener("click", () => {
    if (!kortInsatt) return;
    const pinInput = document.getElementById("pin");
    if (pinInput && pinInput.value.length > 0) {
      pinInput.value = pinInput.value.slice(0, -1);
    }
  });
}

// === GRÖN OK-KNAPP (SKICKAR FORMULÄRET) ===
const btnOk = document.getElementById("btn-ok");
if (btnOk) {
  btnOk.addEventListener("click", () => {
    if (!kortInsatt) return;
    const form = document.getElementById("atm-login-form");
    if (form) {
      form.submit(); // <-- Detta skickar formuläret direkt till index.php!
    }
  });
}

// === RÖD AVBRYT-KNAPP ===
const btnAvbryt = document.getElementById("btn-avbryt");
if (btnAvbryt) {
  btnAvbryt.addEventListener("click", () => {
    taUtKort();
  });
}

// Körs direkt när sidan laddas
document.addEventListener("DOMContentLoaded", () => {
  // Om felmeddelande finns i URL:en vill vi behålla kortet insatt och visa felet
  const urlParams = new URLSearchParams(window.location.search);
  const errorMsg = urlParams.get("error");

  if (errorMsg) {
    kortInsatt = true; // Sätt tillståndet så knapparna fungerar

    // Hämta token om den finns i din DOM
    const carrier = document.getElementById("php-csrf-carrier");
    const actualToken = carrier ? carrier.getAttribute("data-token") : "";

    // Ritar ut formuläret igen med det röda felmeddelandet
    visaStatiskSkärm(`
            <form id="atm-login-form" action="index.php?action=atm_login_process" method="POST" style="display: flex; flex-direction: column; align-items: center; gap: 5px; color: white; font-family: monospace;">
                <input type="hidden" name="csrf_token" value="${actualToken}">
                
                <p style="margin: 0; color: #ff3333; font-weight: bold; font-size: 0.9rem; text-align: center;">${errorMsg}</p>
                
                <input type="hidden" id="card_number" name="card_number" value=""> 
                <p style="margin: 10px 0 0 0; font-size: 0.8rem;">Välj ditt kort igen och försök igen.</p>
            </form>
        `);
    // Nollställ kortet så man måste klicka igen efter ett fel
    setTimeout(() => taUtKort(), 3000);
  } else if (!kortInsatt) {
    visaStatiskSkärm(
      '<p class="screen-idle" style="color: white; font-family: monospace; text-align: center; padding-top: 40px; margin: 0;">Välkommen<br><span style="font-size: 0.8rem; opacity: 0.7;">Sätt in ditt kort</span></p>',
    );
  }
});

// === FRISTÅENDE FUNKTION FÖR ATT BLÄDDRA KORT ===
window.bladdraKort = function (targetPage, e) {
  if (e) {
    e.preventDefault();
    e.stopPropagation();
  }
  const panel = document.querySelector(".external-card-panel");
  if (!panel) return;

  fetch(`index.php?page=atm_cards_api&card_page=${targetPage}&t=${Date.now()}`)
    .then((response) => response.text())
    .then((html) => {
      panel.innerHTML = `<h3>Välj ett bankkort:</h3>` + html;
      initieraKortKlick();
    })
    .catch((err) => console.error("Fel vid laddning av kortsida:", err));
};

// === INITIERA KLICK PÅ KORT ===
function initieraKortKlick() {
  const bankKort = document.querySelectorAll(".bank-card");
  bankKort.forEach((kort) => {
    kort.onclick = function (e) {
      if (kortInsatt) return;

      kortInsatt = true;
      valtKortnummer = e.currentTarget.getAttribute("data-cardnumber");

      const cardAnimationElement = document.getElementById("card");
      if (cardAnimationElement) {
        cardAnimationElement.style.background =
          e.currentTarget.style.background;
        cardAnimationElement.classList.add("insatt");
      }

      visaStatiskSkärm(
        '<p class="screen-idle" style="color: #00d4ff; font-family: monospace; text-align: center;">Läser kort...</p>',
      );

      // Efter 900ms laddar vi PIN-skärmen (Nu pekar formuläret på rätt action!)
      setTimeout(() => {
        const carrier = document.getElementById("php-csrf-carrier");
        const actualToken = carrier ? carrier.getAttribute("data-token") : "";

        visaStatiskSkärm(`
                    <form id="atm-login-form" action="index.php?action=atm_login_process" method="POST" style="display: flex; flex-direction: column; align-items: center; gap: 5px; color: white; font-family: monospace;">
                        <input type="hidden" name="csrf_token" value="${actualToken}">
                        
                        <input type="hidden" id="card_number" name="card_number" value="${valtKortnummer}">
                        
                        <p style="margin: 5px 0 0 0; font-size: 0.9rem; color: #00d4ff;">Kort identifierat</p>
                        <label style="font-size: 0.8rem; margin-top: 10px;">Ange din PIN-kod:</label>
                        <input type="password" id="pin" name="pin" maxlength="4" style="background: rgba(0,0,0,0.5); border: 1px solid #00d4ff; color: #fff; text-align: center; width: 80px; font-size: 1.2rem; letter-spacing: 3px;" readonly>
                    </form>
                `);

        const pinInput = document.getElementById("pin");
        if (pinInput) pinInput.focus();
      }, 900);
    };
  });
}
