<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;

/** @var \Concrete\Core\Localization\Service\Date[] $dh */
/** @var \A3020\SpeedAnalyzer\Entity\Report $report */
/** @var \A3020\SpeedAnalyzer\Event\EventInfo $eventHelper */
/** @var array $items */
?>

<div class="ccm-dashboard-header-buttons btn-group">
    <a
        class="btn btn-default"
        href="<?php echo Url::to('/dashboard/speed_analyzer/reports') ?>">
        <?php echo t('Back to Reports'); ?>
    </a>

    <a
        class="btn btn-danger"
        href="<?php echo $this->action('delete', $report->getId()) ?>">
        <?php echo t('Delete Report'); ?>
    </a>

    <?php
    /** @var \Concrete\Core\Page\Page $reportPage */
    if (isset($reportPage)) {
        ?>
        <a
            class="btn btn-default"
            title="<?php echo t('Visit %s', $report->getPageName()); ?>"
            target="_blank"
            href="<?php echo $reportPage->getCollectionLink() ?>">
            <?php echo t('Open Page'); ?>
             <i class="fa fa-external-link"></i>
        </a>
        <?php
    }
    ?>
</div>

<div class="row">
    <div class="col-sm-12 col-md-4">
        <table class="table">
            <tbody>
                <tr>
                    <td><?php echo t('Total Execution Time') ?>:</td>
                    <td class="text-right">
                        <?php
                        echo number_format($report->getTotalExecutionTime() * 1000);
                        echo ' '.t('ms');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo t('Total Query Time') ?>:</td>
                    <td class="text-right">
                        <?php
                        echo number_format($report->getTotalQueryTime(), 2);
                        echo ' '.t('ms');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo t('Report Date') ?>:</td>
                    <td class="text-right">
                        <?php
                        echo $dh->formatDateTime($report->getCreatedAt());
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-sm-12 col-md-4">
        <table class="table">
            <tbody>
                <tr class="text-muted">
                    <td><?php echo t('Logged in User') ?>:</td>
                    <td class="text-right">
                        <?php
                        $user = $report->getUser();
                        echo $user ? '<a href="'.Url::to('/dashboard/users/search/view/'.$user->getUserID()).'">'.$user->getUserName().'</a>' : t('None');
                        ?>
                    </td>
                </tr>

                <?php
                $ok = '<i class="fa fa-check" title="'.t('Enabled').'"></i>';
                $notOk = '<i class="fa fa-remove" title="'.t('Disabled').'"></i>';
                $requestData = $report->getRequestData();
                ?>
                <tr class="text-muted">
                    <td><?php echo t('Block Cache') ?>:</td>
                    <td class="text-right">
                        <?php
                        echo $requestData->getCacheSetting('blocks') ? $ok : $notOk;
                        ?>
                    </td>
                </tr>
                <tr class="text-muted">
                    <td><?php echo t('Theme CSS Cache') ?>:</td>
                    <td class="text-right">
                        <?php
                        echo $requestData->getCacheSetting('theme_css') ? $ok : $notOk;
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-sm-12 col-md-4">
        <table class="table">
            <tbody>
                <tr class="text-muted">
                    <td><?php echo t('Compress LESS Output') ?>:</td>
                    <td class="text-right">
                        <?php
                        echo $requestData->getCacheSetting('compress_preprocessor_output') ? $ok : $notOk;
                        ?>
                    </td>
                </tr>
                <tr class="text-muted">
                    <td><?php echo t('CSS and JavaScript Cache') ?>:</td>
                    <td class="text-right">
                        <?php
                        echo $requestData->getCacheSetting('assets') ? $ok : $notOk;
                        ?>
                    </td>
                </tr>
                <tr class="text-muted">
                    <td><?php echo t('Overrides Cache') ?>:</td>
                    <td class="text-right">
                        <?php
                        echo $requestData->getCacheSetting('overrides') ? $ok : $notOk;
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-sm-12">
        <p>
            <?php
            echo '<a href="'.$report->getRequestUri().'" target="_blank">'.$report->getRequestUri().'</a>';
            $requestDetails = [$report->getRequestMethod()];
            if ($report->isAjaxRequest()) {
                $requestDetails[] = t('AJAX');
            }
            echo ' <small class="text-muted">('.implode(', ', $requestDetails).')</small>';
            ?>
        </p>
    </div>
</div>


<p class="text-muted">
    <em>
        <?php
        echo t('Tip: Click on a bullet to go to the associated record in the table beneath.');
        ?>
    </em>
</p>

<div class="chart-container">
    <canvas id="loadingTimeChart"></canvas>
</div>

<hr>

<table id="time-table" class="table js-time-table">
    <thead>
        <tr>
            <th style="width: 140px;">
                <?php echo t('Event number'); ?>
            </th>
            <th style="width: 120px;">
                <i class="text-muted launch-tooltip fa fa-question-circle"
                    title="<?php
                    echo t("The first time an event is fired the timer is set to 0. The first event is most often the 'on_before_dispatch' event. It's good to know that total time in milliseconds is not the real execution time. It's simply the time a certain event was triggered.");
                    echo ' ';
                    echo t("1000 milliseconds corresponds with 1 second. Generally speaking, a website that loads fast probably loads within %d milliseconds.", 400);
                    ?>">
                </i>
                <?php echo t('Time'); ?>
            </th>
            <th style="width: 150px;">
                <?php
                echo t('Category');
                ?>
            </th>
            <th>
                <i class="text-muted launch-tooltip fa fa-question-circle"
                   title="<?php echo t("When concrete5 renders a page, various events are fired. This column shows which event was triggered. If more information is available, e.g. the block type or block id, it'll be displayed."); ?>">
                </i>
                <?php echo t('Information'); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($items as $key => $item) {
            // The first item will be 0
            if ($item['difference'] !== 0) {
                ?>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td class="text-muted execution-time-between">
                        <i class="text-muted launch-tooltip fa fa-question-circle"
                           title="<?php echo t("The number of milliseconds between two events."); ?>"
                           ></i>
                        <?php
                        echo t('PHP execution') . ': ';
                        echo '<strong>';
                        echo $item['difference'] === 0 ? '-' : number_format($item['difference']).' '.t('ms');
                        echo '</strong>';

                        if ($item['number_of_queries']) {
                            ?>
                            <br>
                            <i class="text-muted launch-tooltip fa fa-question-circle"
                               title="<?php echo t("The number of SQL queries between two events and the total execution time of those queries.") . ' '
                                   . t("Logging can be disabled via %s.", t('Settings')); ?>"
                            ></i>
                            <?php
                            echo t('MySQL execution') . ': ';
                            echo '<a href="'.Url::to('/ccm/system/speed_analyzer/query/'.$item['event_id']).'"
                            title="'.t('View queries').'"
                            class="number-of-queries dialog-launch" dialog-width="750" dialog-height="500" dialog-modal="true">'.
                                t2('%d query in %s ms', '%d queries in %s ms', $item['number_of_queries'], $item['total_query_time_rounded']).
                                '</a>';
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>

            <tr id="event-<?php echo $item['id'] ?>">
                <td><span class="text-muted"><?php echo $i; ?></span></td>
                <td>
                    <?php
                    echo number_format($item['time'], 0);
                    echo ' '.t('ms');
                    ?>
                </td>
                <td>
                    <?php
                    echo '<span class="badge" style="background-color: '.$item['event_category_color'].'">'.$item['event_category'].'</span> ';
                    ?>
                </td>
                <td>
                    <?php
                    $helpText = $eventHelper->info($item['event']);
                    $helpText = $helpText ? $helpText : t('No help available');
                    echo '<span class="badge event-badge text-muted launch-tooltip" title="' . $helpText . '">';
                        echo $item['event'];
                        echo ' <i class="fa fa-question-circle"></i>';
                    echo '</span>';

                    $html = $item['information']->getHtml();
                    if ($html) {
                        echo '<br><br>';
                        echo $html;
                    }
                    ?>
                </td>
            </tr>
            <?php
            $i++;
        }
        ?>
    </tbody>
</table>

<script>
    var speedAnalyzer = {};

    $(document).ready(function() {
        var json = <?php echo json_encode($items); ?>;

        var ctx = $('#loadingTimeChart').get(0).getContext('2d');

        var data = json.map(function(e) {
            return {
                x: e.time,
                y: e.id,
                event: e.event,
                event_category_color: e.event_category_color,
                information: e.information
            }
        });

        speedAnalyzer.scrollToEvent = function(id) {
            var elem = $('#event-'+id);

            $(elem).css('backgroundColor', '#ffe2ea');

            $('html, body').animate({
                'scrollTop': $(elem).offset().top - 140
            }, 700);

            setTimeout(function() {
                $(elem).css('backgroundColor', 'inherit');
            }, 2000);
        };

        var pointBackgroundColors = [];
        var myChart = new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [{
                    showLine: true,
                    fill: false,
                    borderColor: '#888',
                    borderWidth: 0,
                    pointRadius: 7,
                    pointHoverRadius: 8,
                    pointHitRadius: 10,
                    pointBackgroundColor: pointBackgroundColors,
                    data: data
                }]
            },
            options: {
                annotation: {
                    drawTime: 'beforeDatasetsDraw',
                    annotations: [
                        <?php
                        /** @var array $lineAnnotations */
                        foreach ($lineAnnotations as $annotation) {
                            ?>
                            {
                                type: "line",
                                borderDash: [2, 2],
                                mode: "vertical",
                                scaleID: "x-axis-1",
                                value: <?php echo $annotation['value'] ?>,
                                borderColor: "green",
                                label: {
                                    content: '<?php echo $annotation['label'] ?>',
                                    enabled: true,
                                    yAdjust: 20,
                                    xAdjust: <?php echo $annotation['xAdjust'] ?>,
                                    position: '<?php echo $annotation['position'] ?>'
                                }
                            },
                            <?php
                        }
                        ?>
                    ]
                },
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: '<?php echo t('Time in milliseconds') ?>'
                        },
                        time: {
                            unit: 'millisecond'
                        }
                    }],
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: '<?php echo t('Event Number') ?>'
                        },
                        ticks: {
                            suggestedMin: 0,
                            suggestedMax: 5
                        }
                    }]
                },
                tooltips: {
                    xPadding: 15,
                    yPadding: 15,
                    bodyFontSize: 15,
                    displayColors: false,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var item = data.datasets[0].data[tooltipItem.index] || '';

                            var multistringText = ['<?php echo t('Event') ?>: ' + item.event];

                            if (item.information) {
                                item.information.forEach(function(item) {
                                    if (isNaN(parseFloat(item['type']))) {
                                        multistringText.push(item['type'] + ': ' + item['value']);
                                    } else {
                                        multistringText.push(item['value']);
                                    }
                                });
                            }

                            return multistringText;
                        }
                    }
                }
            }
        });

        // It's not possible to add the point color via the config above.
        // We therefore iterate through the data set again, and then update the chart.
        for (i = 0; i < myChart.data.datasets[0].data.length; i++) {
            pointBackgroundColors.push(myChart.data.datasets[0].data[i].event_category_color);
        }

        myChart.update();

        $('#loadingTimeChart').on('click', function(event) {
            var activePoints = myChart.getElementsAtEvent(event);
            var firstPoint = activePoints[0];
            if (firstPoint) {
                var value = myChart.data.datasets[firstPoint._datasetIndex].data[firstPoint._index];

                // The y-axes is also the event number
                speedAnalyzer.scrollToEvent(value.y);
            }
        });
    });
</script>
