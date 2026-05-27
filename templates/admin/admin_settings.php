<?php $pageTitle = 'Admin – Settings'; ?>
<?php require __DIR__ . '/admin_layout.php'; ?>

<h1>Settings</h1>

<form method="POST" action="index.php?page=admin_settings_save">
    <?= csrf_field() ?>

    <div class="card">
        <h3>Gränssnitt</h3>
        <div class="form-group">   
            <label for="color_mode">Färgtema</label>
            <select id="color_mode" name="color_mode">
                <option value="light">Light</option>
                <option value="red">Red</option>
                <option value="auto" selected>Dark (Default)</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Spara ändringar</button>
    </div>
</form>

<div class="card card-seed">
    <h3>Testdata (Seed)</h3>
    <p class="card-description">Hantera fiktiva kunder, konton och transaktioner för utveckling.</p>
    
    <div class="btn-flex">
        <form method="POST" action="index.php?page=admin_settings_action">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="seed_data">
            <button type="submit" class="btn btn-primary">Seed Data</button>
        </form>

        <form method="POST" action="index.php?page=admin_settings_action" onsubmit="return confirm('Är du säker på att du vill radera all testdata?');">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="clear_data">
            <button type="submit" class="btn btn-danger">Remove Data</button>
        </form>
    </div>
</div>

<div class="card card-danger">
    <h3>Databasadministration</h3>
    <p class="card-description">Varning: Destruktiva handlingar som påverkar tabellstrukturen.</p>
    
    <div class="btn-flex">
        <form method="POST" action="index.php?page=admin_settings_action">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="create_db">
            <button type="submit" class="btn btn-primary">Create Database</button>
        </form>

        <form method="POST" action="index.php?page=admin_settings_action" onsubmit="return confirm('⚠️ KRITISK VARNING: Detta raderar ALLA tabeller och all data permanent. Vill du fortsätta?');">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="drop_db">
            <button type="submit" class="btn btn-danger">Remove Database</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>            