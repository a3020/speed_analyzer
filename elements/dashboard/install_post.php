<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;
?>
<p><?php echo t('Congratulations, the add-on has been installed!'); ?></p>
<br>

<p>
    <strong><?php echo t('You can find the add-on here:'); ?></strong>
    <a class="btn btn-default" href="<?php echo Url::to('/dashboard/speed_analyzer') ?>">
        <?php
        echo t('Dashboard / Speed Analyzer');
        ?>
    </a>
</p>
