<?php

namespace A3020\SpeedAnalyzer\Event;

class EventInfo
{
    /**
     * @param string $event
     *
     * @return string
     */
    public function info($event)
    {
        $info = '';

        switch ($event) {
            case 'on_speed_analyzer_started':
                $info = t('Triggered in the %s class.', t('Speed Analyzer').' Package Controller').' '.t('Runs after %s started (see the %s method).', t('Speed Analyzer'), 'on_start');
                break;
            case 'on_speed_analyzer_track':
                $info = t("Custom %s event for analyzing purposes.", t('Speed Analyzer'));
                break;
            case 'on_before_dispatch':
                $info = t("Triggered in the %s class.", "DefaultRunner").' '.t("Runs when the request is handled and after on_start methods on all the packages have been called.");
            break;
            case 'on_header_required_ready':
                $info = t("Triggered in %s file.", "header_required.php").' '.t("Runs when the header is rendered and when e.g. the meta tags and page title are used in the output.");
            break;
            case 'on_logger_create':
                $info = t("Triggered in the %s class.", "Logger").' '.t("Runs after a new instance of %s is created.", "Logger");
                break;
            case 'on_locale_load':
                $info = t("Triggered in the %s class.", "en_US").' '.t("Runs when the locale (e.g. %s) changes.", "en_US");
            break;
            case 'on_page_view':
                $info = t("Triggered in the %s class.", "ResponseFactory").' '.t("Runs when a page is requested and the page is valid. A statistics package would at this point qualify the request as a page hit. Runs before the on_start method of a page controller has been called. At this point no blocks etc. from the page have been loaded yet.");
                break;
            case 'on_start':
                $info = t("Triggered in the %s class.", "View").' '.t("Runs before the page starts rendering and before the on_before_render method has been called.");
                break;
            case 'on_before_render':
                $info = t("Triggered in the %s class.", "View").' '.t("Runs before a template is included / to be rendered.");
                break;
            case 'on_block_load':
                $info = t("Triggered in the %s class.", "BlockController").' '.t("Runs after the block record is loaded from the database (or the cache). Once this is done, a block is ready to be rendered. Note that all blocks of an Area are first loaded, before they are rendered.");
                break;
            case 'on_block_before_render':
                $info = t("Triggered in the %s class.", "BlockView").' '.t("Runs before the template of a block is included / to be rendered, and after the Block Controller has ran.");
                break;
            case 'on_user_login':
                $info = t("Triggered after a user is authenticated and just before the user is redirected.");
                break;
            case 'on_render_complete':
                $info = t("Triggered in the %s class.", "View").' '.t("Runs when the whole page is rendered. The rendered HTML will then be sent as a Response.");
                break;
            case 'on_page_output':
                $info = t("Triggered in the %s class.", "PageView").' '.t("Runs when a page finished rendering and is about to be written to the cache (if needed).");
                break;
            case 'on_shutdown':
                $info = t("Triggered in the %s class.", "Application").' '.t("Runs before the application is shutdown and e.g. the database connection is closed.");
                break;
        }

        return $info;
    }
}
