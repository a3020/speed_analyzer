<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;
?>


<div class="ccm-dashboard-header-buttons btn-group">
    <a
        class="btn btn-default"
        href="<?php echo Url::to('/dashboard/speed_analyzer/reports') ?>">
        <?php echo t('Reports'); ?>
    </a>
</div>

<div class="ccm-dashboard-content-inner">
    <form method="post" action="<?php echo $this->action('save'); ?>">
        <?php
        /** @var $token \Concrete\Core\Validation\CSRF\Token */
        echo $token->output('a3020.speed_analyzer.settings');
        ?>

        <div class="form-group">
            <label class="control-label launch-tooltip"
                   title="<?php echo t("When disabled, %s won't track the events and won't generate reports. On production you probably want it disabled.", t('Speed Analyzer')); ?>"
                   for="isEnabled">
                <?php
                /** @var bool $isEnabled */
                echo $form->checkbox('isEnabled', 1, $isEnabled);
                ?>
                <?php echo t('Enable Speed Analyzer'); ?>
            </label>

            <?php
            /** @var bool $isProduction */
            if ($isProduction) {
                ?>
                <br>
                <small>
                    <?php
                    echo t("Be aware that enabling %s has a slight impact on performance.", t('Speed Analyzer'));
                    ?>
                </small>
                <?php
            }
            ?>
        </div>

        <div class="form-group">
            <label class="control-label launch-tooltip"
                   title="<?php echo t("You might not want reports if a page is checked-out by the user at that moment."); ?>"
                   for="enabledInEditMode">
                <?php
                /** @var bool $enabledInEditMode */
                echo $form->checkbox('enabledInEditMode', 1, $enabledInEditMode);
                ?>
                <?php echo t('Enable reports when a page is in Edit Mode'); ?>
            </label>
        </div>

        <div class="form-group">
            <label class="control-label launch-tooltip"
                   title="<?php echo t("This will generate reports for all dashboard pages, except the Speed Analyzer section."); ?>"
                   for="enabledInDashboard">
                <?php
                /** @var bool $enabledInDashboard */
                echo $form->checkbox('enabledInDashboard', 1, $enabledInDashboard);
                ?>
                <?php echo t('Enable reports when the user is in the dashboard'); ?>
            </label>
        </div>

        <div class="form-group">
            <label class="control-label launch-tooltip"
                   title="<?php echo t("%s can log the SQL queries and the total execution time per query. The number of queries will be presented in the report details table.", t('Speed Analyzer')); ?>"
                   for="logSqlQueries">
                <?php
                /** @var bool $logSqlQueries */
                echo $form->checkbox('logSqlQueries', 1, $logSqlQueries);
                ?>
                <?php echo t('Enable logging of SQL queries'); ?>
            </label>
        </div>

        <div class="form-group">
            <label class="control-label launch-tooltip"
                   title="<?php echo t("Check this option if you only want one report per page. Meaning that with each request old reports for a page are deleted."); ?>"
                   for="overwriteReports">
                <?php
                /** @var bool $overwriteReports */
                echo $form->checkbox('overwriteReports', 1, $overwriteReports);
                ?>
                <?php echo t('Enable overwriting reports'); ?>
            </label>
        </div>

        <div class="form-group">
            <label class="control-label launch-tooltip"
                   title="<?php echo t("AJAX stands for %s. Disable this setting if you only want reports of a normal page load.", "Asynchronous JavaScript And XML"); ?>"
                   for="trackAjaxRequests">
                <?php
                /** @var bool $trackAjaxRequests */
                echo $form->checkbox('trackAjaxRequests', 1, $trackAjaxRequests);
                ?>
                <?php echo t('Enable tracking AJAX requests'); ?>
            </label>
        </div>

        <div class="form-group">
            <label class="control-label launch-tooltip"
                   title="<?php echo t("Only enable this if your own code doesn't override the Event Dispatcher already (which is not likely). If the class is overridden, %s can catch all events that are triggered, including all of your custom event. This results in a better and more complete time line.", t('Speed Analyzer')); ?>"
                   for="overrideEventDispatcher">
                <?php
                /** @var bool $overrideEventDispatcher */
                echo $form->checkbox('overrideEventDispatcher', 1, $overrideEventDispatcher);
                ?>
                <?php echo t('Override the concrete5 Event Dispatcher'); ?>
            </label>
        </div>

        <div class="form-group custom-events-container <?php echo $overrideEventDispatcher ? 'hide' : '' ?>">
            <label class="control-label launch-tooltip"
                   title="<?php echo t("If you use custom events you can add them here. %s will then record the times when they are fired. Use one event per line.", t("Speed Analyzer")); ?>"
                   for="domains">
                <?php echo t('Custom events'); ?>
            </label>
            <?php
            /** @var string $customEvents */
            echo $form->textarea('customEvents', $customEvents, [
                'placeholder' => t('Leave blank to not track your own events'),
                'style' => 'min-height: 140px; max-width: 400px',
            ]);
            ?>
        </div>

        <div class="form-group">
            <label class="control-label launch-tooltip"
                   title="<?php echo t("This option is to only generate reports for the really slow requests."); ?>"
                   for="overrideEventDispatcher">
                <?php echo t('Only generate a report if the request took longer than x-milliseconds'); ?>
            </label>

            <?php
            /** @var int $writeIfExecTimeLongerThan */
            echo $form->number('writeIfExecTimeLongerThan', $writeIfExecTimeLongerThan, [
                'placeholder' => t('Leave blank to not set a minimum'),
            ]);
            ?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="pull-right btn btn-primary" type="submit"><?php echo t('Save') ?></button>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#overrideEventDispatcher').change(function(e) {
        if ($(this).is(':checked')) {
            $('.custom-events-container').addClass('hide');
        } else {
            $('.custom-events-container').removeClass('hide');
        }
    });
});
</script>
