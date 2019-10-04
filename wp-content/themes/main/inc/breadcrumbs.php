<?php if (0): ?>
    <div class="container">
        <div class="breadcrumbs">
            <span><a href="#"><?php icon('home', 'svg-home') ?></a></span>
            <span class="divider"><?php icon('arrow-right') ?></span>
            <span><a href="#">Події</a></span>
            <span class="divider"><?php icon('arrow-right') ?></span>
            <span>Музична Платформа</span>
        </div>
    </div>
<?php endif ?>
<div class="container">
    <div class="breadcrumbs">
        <span><a href="/">^</a></span>
        <span class="divider">></span>
        <?php if (function_exists('bcn_display')) {
            bcn_display();
        } ?>
    </div>
</div>
