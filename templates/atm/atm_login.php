
<div class="atm-embedded-login">
    
    <div class="atm-login-header">
        <h2>VÄLKOMMEN</h2>
        <p>Mata in kort & PIN</p>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="atm-error-msg" style="color: #ff3333; font-weight: bold; margin-bottom: 10px;">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php" id="atm-login-form" class="atm-login-form">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="atm_login_process">

        <div class="atm-form-group">
            <label for="card_number">KORTNUMMER:</label>
            <input
                type="text"
                id="card_number"
                name="card_number"
                maxlength="16"
                autocomplete="off"
                required
                placeholder="16 siffror"
                value="<?= htmlspecialchars($_POST['card_number'] ?? '') ?>"
            >
        </div>

        <div class="atm-form-group">
            <label for="pin">PIN-KOD:</label>
            <input
                type="password"
                id="pin"
                name="pin"
                maxlength="4"
                autocomplete="off"
                required
                placeholder="••••"
            >
        </div>

        <button type="submit" style="display: none;"></button>
    </form>

    <div class="atm-login-footer">
        Tryck på gröna [OK] för att bekräfta
    </div>
</div>