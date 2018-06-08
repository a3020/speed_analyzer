<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var string|false $clientCountry */
/** @var string|false $serverCountry */
/** @var string $serverIp */
/** @var string $clientIp */
?>

<p>
    <?php
    echo t('Server IP: %s', '<a href="https://check-host.net/ip-info?host='.$serverIp.'" target="_blank">'.$serverIp.'</a>');
    echo $serverCountry ? ' ('.$serverCountry.')' : '';

    echo '<span style="margin: 0 20px;">|</span>';
    echo t('Your IP: %s', '<a href="https://check-host.net/ip-info?host='.$clientIp.'" target="_blank">'.$clientIp.'</a>');
    echo $clientCountry ? ' ('.$clientCountry.')' : '';
    ?>
</p>

<?php
if (!$clientCountry || !$serverCountry) {
    ?>
    <div class="alert alert-warning m-b-none">
        <?php
        if (version_compare("8.3.0", APP_VERSION, '>')) {
            echo t("Your concrete5 installation doesn't support geo location. Please upgrade to a newer version.");
        } elseif (!$clientCountry && $serverCountry) {
            echo t("Your geo location couldn't be determined.");
        } elseif ($clientCountry && !$serverCountry) {
            echo t("The geo location of the server couldn't be determined.");
        } else {
            echo t("The geo location for you and the server couldn't be determined.");
        }
        ?>
    </div>
    <?php
} else {
    if ($clientCountry !== $serverCountry) {
        ?>
        <div class="alert alert-warning m-b-none">
            <?php
            echo t("The server doesn't appear to be in the same country as you are. ".
                "Therefore, connection / loading times may be higher. Consider migrating your server to %s if most of your visitors also come from %s.",
                $clientCountry, $clientCountry
            );
            ?>
        </div>
        <?php
    } else {
        echo t("The server seems to be in the same country as you are. That's good for loading times!");
    }
}
