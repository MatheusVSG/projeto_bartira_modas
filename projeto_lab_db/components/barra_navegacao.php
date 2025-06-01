<?php
$linksPadrao = [
    
];

// Junta os arrays se $linksAdicionais existir
$links = isset($linksAdicionais) ? array_merge($linksPadrao, $linksAdicionais) : $linksPadrao;
?>

<div class="position-sticky top-0 start-0 z-3 py-3 d-flex align-items-center bg-dark gap-2">
    <?php foreach ($links as $link): ?>
        <a href="<?= htmlspecialchars($link['caminho']) ?>" class="btn <?= htmlspecialchars($link['cor']) ?>">
            <?= htmlspecialchars($link['titulo']) ?>
        </a>
    <?php endforeach; ?>
</div>
