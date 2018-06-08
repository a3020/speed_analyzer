<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var bool $imageOptimizerInstalled */
/** @var bool $minifyHtmlInstalled */
?>

<div>
    <?php
    if ($imageOptimizerInstalled) {
        echo '<i class="fa fa-check"></i> ';
        echo t('%s is installed.', t('Image Optimizer'));
    } else {
        echo '<i class="fa fa-warning"></i> ';
        echo t('%s is not installed.',
            '<a href="https://www.concrete5.org/marketplace/addons/image-optimizer/" target="_blank">'.t('Image Optimizer').'</a>'
        );
        echo ' '.t("It's recommended to optimize images for faster rendering. It's a quick win and it improves your SEO score.");
    }
    ?>
</div>

<div>
    <?php
    if ($minifyHtmlInstalled) {
        echo '<i class="fa fa-check"></i> ';
        echo t('%s is installed.', t('Minify HTML'));
    } else {
        echo '<i class="fa fa-warning"></i> ';
        echo t('%s is not installed.',
            '<a href="https://www.concrete5.org/marketplace/addons/minify-html/" target="_blank">'.t('Minify HTML').'</a>'
        );
        echo ' '.t("The minifier removes HTML comments and tries to reduce the size of the HTML output that is sent back to the browser.");
    }
    ?>
</div>
