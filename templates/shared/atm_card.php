<?php
$currentCardPage = isset($_GET['card_page']) ? (int)$_GET['card_page'] : 1;

if ($currentCardPage < 1) $currentCardPage = 1;

$limit = 5; 

$users = $userRepo->findAllUsersOnly($currentCardPage, $limit);
$totalUsers = $userRepo->countTotalUsers();

$totalPages = ceil($totalUsers / $limit);
?>

<div class="atm-card-selector">
    <?php if (!empty($users)): ?>
        <?php foreach ($users as $u): ?>
            <div class="bank-card" data-cardnumber="<?php echo htmlspecialchars($u['card_number']); ?>">
                <div class="card-chip"></div>
                <div class="card-logo">BANK</div>
                
                <p class="card-number-text">
                    <?php 
                        $nr = htmlspecialchars($u['card_number']);
                        echo substr($nr, 0, 4) . ' **** **** ' . substr($nr, -4);
                    ?>
                </p>
                <p class="card-holder-text"><?php echo htmlspecialchars($u['name']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: #ff5555; font-size: 0.8rem; font-style: italic; margin: 20px 0;">Inga fler kort.</p>
    <?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
    <div class="card-pagination" style="display: flex; justify-content: space-between; width: 140px; margin-top: 10px; gap: 10px;">
        
        <?php if ($currentCardPage > 1): ?>
            <a href="index.php?page=real-atm-nr1&card_page=<?php echo $currentCardPage - 1; ?>" 
               class="pag-btn" 
               style="text-decoration: none; cursor: pointer; color: #ffd700; background: rgba(0,0,0,0.6); padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; border: 1px solid rgba(255,215,0,0.3); flex: 1; text-align: center;">
               ◀
            </a>
        <?php else: ?>
            <div class="pag-btn disabled" 
                 style="color: #555; background: rgba(0,0,0,0.2); padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; border: 1px solid #333; flex: 1; text-align: center; cursor: not-allowed;">
               ◀
            </div>
        <?php endif; ?>

        <?php if ($currentCardPage < $totalPages): ?>
            <a href="index.php?page=real-atm-nr1&card_page=<?php echo $currentCardPage + 1; ?>" 
               class="pag-btn" 
               style="text-decoration: none; cursor: pointer; color: #ffd700; background: rgba(0,0,0,0.6); padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; border: 1px solid rgba(255,215,0,0.3); flex: 1; text-align: center;">
               ▶
            </a>
        <?php else: ?>
            <div class="pag-btn disabled" 
                 style="color: #555; background: rgba(0,0,0,0.2); padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; border: 1px solid #333; flex: 1; text-align: center; cursor: not-allowed;">
               ▶
            </div>
        <?php endif; ?>

    </div>
    <div style="font-size: 0.7rem; color: rgba(255,255,255,0.5); text-align: center; margin-top: 4px;">
        Sida <?php echo $currentCardPage; ?> av <?php echo $totalPages; ?>
    </div>
<?php endif; ?>
