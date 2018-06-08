<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;

$sections = [
    'environment' => t('Environment'),
    'location' => t('Location'),
    'packages' => t('Packages'),
];
?>

<div class="ccm-dashboard-content-inner page-diagnosis">
    <?php
    foreach ($sections as $handle => $name) {
        ?>
        <header class="diagnosis-header"><?php echo $name ?></header>
        <section class="diagnosis-section" id="<?php echo $handle ?>">
            <?php echo t('Loading...'); ?>
        </section>
        <?php
    }
    ?>
</div>

<script>
    <?php
    foreach ($sections as $handle => $name) {
        ?>
        $('#<?php echo $handle ?>').load('<?php echo Url::to('/ccm/system/speed_optimizer/diagnosis/'.$handle) ?>');
        <?php
    }
    ?>
</script>
