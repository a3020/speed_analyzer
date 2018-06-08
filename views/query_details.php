<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var \A3020\SpeedAnalyzer\Entity\ReportEventQuery[] $queries */
?>

<div class="ccm-ui">
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th><?php echo t('Query'); ?></th>
            <th style="width: 130px"><?php echo t('Execution time'); ?></th>
        </tr>
        </thead>

        <?php
        $i = 0;
        foreach ($queries as $query) {
            ?>
            <tr>
                <td>
                    <a href="javascript:" class="copy-to-clipboard-link js-tooltip js-copy" data-toggle="tooltip" data-placement="right" data-copy="<?php echo $query->getQuery() ?>">
                        <?php echo $query->getQuery() ?>
                    </a>
                </td>
                <td>
                    <?php
                    echo t('%s ms', number_format($query->getExecutionTime() * 1000, 2));
                    ?>
                </td>
            </tr>
            <?php
            $i++;
        }
        ?>
    </table>

    <div class="dialog-buttons">
        <button class="btn btn-default pull-right" onclick="jQuery.fn.dialog.closeTop()"><?php echo t('Close')?></button>
    </div>
</div>

<script>
function copyToClipboard(text, el) {
    var copyTest = document.queryCommandSupported('copy');
    var elOriginalText = el.attr('data-original-title');

    if (copyTest === true) {
        var copyTextArea = document.createElement("textarea");
        copyTextArea.value = text;
        document.body.appendChild(copyTextArea);
        copyTextArea.select();
        try {
            var successful = document.execCommand('copy');
            var msg = successful ? '<?php echo t('Copied to clipboard!') ?>' : '<?php echo t('Whoops, not copied!') ?>';
            el.attr('data-original-title', msg).tooltip('show');
        } catch (err) {
            console.log('Oops, unable to copy');
        }
        document.body.removeChild(copyTextArea);
        el.attr('data-original-title', elOriginalText);
    } else {
        // Fallback if browser doesn't support .execCommand('copy')
        window.prompt("<?php echo t('Copy to clipboard: Ctrl+C or Command+C, Enter'); ?>", text);
    }
}

$(document).ready(function() {
    $('.js-tooltip').tooltip();

    $('.js-copy').click(function() {
        var text = $(this).attr('data-copy');
        var el = $(this);
        copyToClipboard(text, el);
    });
});
</script>
