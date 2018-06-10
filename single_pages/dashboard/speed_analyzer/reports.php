<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;
?>

<div class="ccm-dashboard-header-buttons btn-group">
    <?php
    /** @var bool $hasData */
    if ($hasData) {
        ?>
        <a
            class="btn btn-danger btn-delete-all-reports"
            data-link="<?php echo $this->action('deleteAll') ?>"
            onclick="deleteAllReports(this)">
            <?php echo t('Delete all Reports'); ?>
        </a>
        <?php
    }
    ?>

    <a
        class="btn btn-default"
        href="<?php echo Url::to('/dashboard/speed_analyzer/settings') ?>">
        <?php echo t('Settings'); ?>
    </a>
</div>

<?php
/** @var bool $isEnabled */
if ($isEnabled === false) {
    ?>
    <div class="alert alert-warning">
        <?php
        echo t('%s is currently disabled. No new reports will be generated. Go to %s to enable %s.',
            t('Speed Analyzer'),
            t('Settings'),
            t('Speed Analyzer')
        );
        ?>
    </div>
    <?php
}

/** @var bool $isFullPageCachingEnabled */
if ($isEnabled && $isFullPageCachingEnabled) {
    ?>
    <div class="alert alert-warning">
        <?php
        echo t('Full Page Caching is enabled. %s will only make reports if a user is logged in. <a href="%s">Change cache settings</a>.',
            t('Speed Analyzer'),
            Url::to('/dashboard/system/optimization/cache')
        );
        ?>
    </div>
    <?php
}

if ($isEnabled && !$hasData) {
    ?>
    <div class="alert alert-warning">
        <?php
        echo t('%s is enabled, but no data is available yet. Please visit one of your pages to generate a report.', t('Speed Analyzer'));
        ?>
    </div>

    <a target="_blank" class="btn btn-primary" href="<?php echo Url::to('/'); ?>">
        <?php echo t('Visit Home page'); ?>
        <i class="fa fa-external-link"></i>
    </a>

    <?php
    return;
}
?>

<table class="table table-striped table-bordered" id="tbl-pages">
    <thead>
        <tr>
            <th><?php echo t('Requested Page'); ?></th>
            <th style="width: 220px"><?php echo t('User') ?></th>
            <th style="width: 135px"><?php echo t('Execution Time') ?></th>
            <th style="width: 65px"><?php echo t('Method') ?></th>
            <th style="width: 65px"><?php echo t('AJAX') ?></th>
            <th style="width: 135px"><?php echo t('Report Date') ?></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<script>
    var deleteAllReports = function(e) {
        if (confirm('<?php echo t('Are you sure you want to delete all reports?') ?>')) {
            window.location.href = $(e).attr('data-link');
        }
    };

    $(document).ready(function() {
        $('#tbl-pages').DataTable({
            searching: false,
            serverSide: true,
            ajax: '<?php echo Url::to('/ccm/system/speed_analyzer/reports'); ?>',
            lengthMenu: [[20, 50, 100], [20, 50, 100]],
            columns: [
                {
                    data: function(row, type, val) {
                        return '<a href="<?php echo $this->action('details'); ?>/' + row.id + '/'+row.page_id +'">' + row.page_name + '</a>' +
                                '<br><small class="text-muted">' + row.request_uri + '</small>'
                    }
                },
                {
                    data: "user"
                },
                {
                    data: "execution_time"
                },
                {
                    data: "request_method"
                },
                {
                    data: "is_ajax"
                },
                {
                    data: "created_at"
                }
            ],
            order: [[5, "desc"]]
        });
    })
</script>
