<?php

namespace Swc;

class AdminLayout {
    public static function render($content, array $messages) {
        ob_start();
        ?>

        <div class='wrap swc'>
            <h1><?= esc_html(get_admin_page_title()); ?></h1>

            <?= $content; ?>
        </div>

        <?php
        return ob_get_clean();
    }
}
