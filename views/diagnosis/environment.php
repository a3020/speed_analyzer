<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var int $realPathCacheSize */
/** @var int $maxRealPathCacheSize */
?>

<table class="table">
    <thead>
        <tr>
            <th><?php echo t('Name') ?></th>
            <th><?php echo t('Version') ?></th>
            <th><?php echo t('Version available') ?></th>
        </tr>
    </thead>
    <tr>
        <td>
            <a href="https://www.concrete5.org/download" target="_blank">
                <?php
                echo t('concrete5');
                ?>
            </a>
        </td>
        <td>
            <?php
            /** @var string|null $concrete5VersionAvailable */
            /** @var string $concrete5Version */
            echo version_compare($concrete5VersionAvailable, $concrete5Version, '>') ?
                '<i class="fa fa-warning"></i> '.$concrete5Version :
                '<i class="fa fa-check"></i> '.$concrete5Version;
            ?>
        </td>
        <td>
            <?php
            echo version_compare($concrete5VersionAvailable, $concrete5Version, '>') ? $concrete5VersionAvailable : '-';
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <a href="http://php.net/releases/" target="_blank">
                <?php
                echo t('PHP');
                ?>
            </a>
        </td>
        <td>
            <?php
            /** @var string $phpVersion */
            /** @var array|null $phpVersionAvailable */
            echo is_array($phpVersionAvailable) && count($phpVersionAvailable) ?
                '<i class="fa fa-warning"></i> '.$phpVersion :
                '<i class="fa fa-check"></i> '.$phpVersion;
            echo '<br>';

            echo '<div class="text-muted">';
            echo t('Real path cache size: %s bytes (max: %s bytes)', $realPathCacheSize, $maxRealPathCacheSize);
            echo ' <i class="launch-tooltip fa fa-question-circle"
               title="'.t("The '%s' is used by PHP to cache the real file system paths of file names referenced instead of looking them up each time.
                Every time you perform any of the various file functions or include/require a file and use a relative path,
                PHP has to look up where that file really exists. PHP caches those values so it doesn't have to search the current
                working directory and include_path for the file you are working on.", 'realpath_cache_size').'"></i>';
            echo '</div>';
            ?>
        </td>
        <td>
            <?php
            if (is_array($phpVersionAvailable)) {
                echo count($phpVersionAvailable) === 0 ? '-' : implode(', ', $phpVersionAvailable);
            } else {
                echo t('Could not retrieve latest version');
            }
            ?>
        </td>
    </tr>

    <tr>
        <td>
            <a href="http://php.net/manual/en/intro.opcache.php" target="_blank">
                <?php
                echo t('OPcache');
                ?>
            </a>

            <i class="text-muted launch-tooltip fa fa-question-circle"
               title="<?php echo t("%s compiles and caches PHP scripts. It's recommended in live environments.", "OPcache") ?>"></i>
        </td>
        <td>
            <?php
            /** @var bool $opcacheEnabled */
            /** @var bool $opcacheValidateTimestamps */
            echo $opcacheEnabled ?
                '<i class="fa fa-check"></i> '.t('Enabled') :
                '<i class="fa fa-warning"></i> '.t('Not enabled');


            if ($opcacheEnabled) {
                echo '<div class="text-muted">';

                if ($opcacheValidateTimestamps) {
                    echo '<div>';
                    echo '<i class="fa fa-warning"></i> ';
                    echo t('%s should probably disabled', 'opcache.validate_timestamps');

                    echo ' <i class="text-muted launch-tooltip fa fa-question-circle"
                   title="' . t("If enabled, OPcache will check for updated scripts every opcache.revalidate_freq seconds. 
                    When this directive is disabled, you must reset OPcache manually via opcache_reset(), opcache_invalidate() 
                    or by restarting the Web server for changes to the filesystem to take effect.") . '"></i>';
                    echo '</div>';
                }

                /** @var array $opcacheStatus */
                if ($opcacheStatus['cache_full']) {
                    echo '<div>';
                    echo '<i class="fa fa-warning"></i> ';
                    echo t('The %s cache is full.', 'OPcache');
                    echo '</div>';
                }

                $usedMemory = $opcacheStatus['memory_usage']['used_memory'] > 1024 ? round($opcacheStatus['memory_usage']['used_memory'] / 1024) : 0;
                $freeMemory = $opcacheStatus['memory_usage']['free_memory'] > 1024 ? round($opcacheStatus['memory_usage']['free_memory'] / 1024) : 0;

                echo '<div>';
                echo t('Used memory: %s KB.', $usedMemory).'<br>';
                echo t('Free memory: %s KB.', $freeMemory).'<br>';
                echo '</div>';

                echo '</div>';
            }
            ?>
        </td>
        <td>
            <span class="text-muted">-</span>
        </td>
    </tr>

    <tr>
        <td>
            <a href="https://xdebug.org/" target="_blank">
                <?php
                echo t('Xdebug');
                ?>
            </a>

            <i class="text-muted launch-tooltip fa fa-question-circle"
               title="<?php echo t("%s assists in debugging and development. It's not recommended in live environments.", "Xdebug") ?>"></i>
        </td>
        <td>
            <?php
            /** @var string $xdebugVersion */
            echo $xdebugVersion ?
                '<i class="fa fa-warning"></i> '.$xdebugVersion :
                '<i class="fa fa-check"></i> '.t('Not installed');
            ?>
        </td>
        <td>
            <span class="text-muted">-</span>
        </td>
    </tr>

    <tr>
        <td>
            <?php
            echo t('MySQL');
            ?>
        </td>
        <td>
            <?php
            /** @var string $mysqlVersion */
            echo $mysqlVersion;
            ?>
        </td>
        <td>
            <span class="text-muted"><?php echo t('Unknown'); ?></span>
        </td>
    </tr>
</table>

<script>
    $(function () {
        $(".launch-tooltip").tooltip();
    });
</script>
