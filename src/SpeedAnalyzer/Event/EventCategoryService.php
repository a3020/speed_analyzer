<?php

namespace A3020\SpeedAnalyzer\Event;

class EventCategoryService
{
    /**
     * Find an event and return an object
     *
     * @param string $event
     *
     * @return EventCategory|null
     */
    public function find($event)
    {
        foreach ($this->categoryEvents() as $category => $events) {
            if (in_array($event, $events)) {
                return new EventCategory($event, $category);
            }
        }

        return new EventCategory($event, EventCategory::CUSTOM);
    }

    /**
     * Find the events that belong to a certain category
     *
     * @param string $category
     *
     * @return array
     */
    public function findByCategory($category)
    {
        $events = [];

        switch ($category) {
            case EventCategory::APPLICATION:
                $events[] = 'on_start';
                $events[] = 'on_before_dispatch';
                $events[] = 'on_before_render';
                $events[] = 'on_render_complete';
                $events[] = 'on_shutdown';
                $events[] = 'on_cache_flush';
                $events[] = 'on_before_console_run';
                $events[] = 'on_after_console_run';
                $events[] = 'on_entity_manager_configure';
                $events[] = 'on_locale_load';
                $events[] = 'on_logger_create';
                $events[] = 'on_header_required_ready';
            break;
            case EventCategory::USER:
                $events[] = 'on_before_user_add';
                $events[] = 'on_user_add';
                $events[] = 'on_user_update';
                $events[] = 'on_user_change_password';
                $events[] = 'on_user_delete';
                $events[] = 'on_user_validate';
                $events[] = 'on_user_activate';
                $events[] = 'on_user_deactivate';
                $events[] = 'on_user_login';
                $events[] = 'on_user_attributes_saved';
                $events[] = 'on_group_add';
                $events[] = 'on_group_update';
                $events[] = 'on_group_delete';
                $events[] = 'on_user_enter_group';
                $events[] = 'on_user_exit_group';
            break;
            case EventCategory::PAGE:
                $events[] = 'on_page_view';
                $events[] = 'on_page_output';
                $events[] = 'on_page_add';
                $events[] = 'on_page_get_icon';
                $events[] = 'on_page_update';
                $events[] = 'on_page_delete';
                $events[] = 'on_page_move';
                $events[] = 'on_page_duplicate';
                $events[] = 'on_page_move_to_trash';
                $events[] = 'on_compute_canonical_page_path';
                $events[] = 'on_multilingual_page_relate';
                $events[] = 'on_page_type_publish';
                $events[] = 'on_page_type_save_composer_form';
                $events[] = 'on_page_version_add';
                $events[] = 'on_page_version_approve';
                $events[] = 'on_page_version_submit_approve';
                $events[] = 'on_page_version_deny';
            break;
            case EventCategory::FILE:
                $events[] = 'on_file_add';
                $events[] = 'on_file_set_password';
                $events[] = 'on_file_download';
                $events[] = 'on_file_delete';
                $events[] = 'on_file_duplicate';
                $events[] = 'on_file_version_add';
                $events[] = 'on_file_version_deny';
                $events[] = 'on_file_version_approve';
                $events[] = 'on_file_version_duplicate';
                $events[] = 'on_file_version_update_title';
                $events[] = 'on_file_version_update_tags';
                $events[] = 'on_file_version_update_description';
                $events[] = 'on_file_version_update_contents';
                $events[] = 'on_file_set_add';
                $events[] = 'on_file_set_delete';
                $events[] = 'on_file_added_to_set';
                $events[] = 'on_file_removed_from_set';
            break;
            case EventCategory::BLOCK:
                $events[] = 'on_block_load';
                $events[] = 'on_block_add';
                $events[] = 'on_block_edit';
                $events[] = 'on_block_delete';
                $events[] = 'on_block_before_render';
                $events[] = 'on_block_duplicate';
            break;
            case EventCategory::OTHER:
                $events[] = 'on_private_message_marked_not_new';
                $events[] = 'on_private_message_marked_as_read';
                $events[] = 'on_private_message_delete';
                $events[] = 'on_private_message_over_limit';
                $events[] = 'on_new_conversation_message';
                $events[] = 'on_job_install';
                $events[] = 'on_job_uninstall';
                $events[] = 'on_before_job_execute';
                $events[] = 'on_job_execute';
                $events[] = 'on_get_countries_list';
                $events[] = 'on_get_states_provinces_list';
                $events[] = 'on_sitemap_xml_ready';
                $events[] = 'on_sitemap_xml_addingpage';
                $events[] = 'on_page_feed_output';
        }

        return $events;
    }

    /**
     * Get a list of all event categories
     *
     * @return array
     */
    public function categories()
    {
        return [
            EventCategory::APPLICATION,
            EventCategory::USER,
            EventCategory::PAGE,
            EventCategory::FILE,
            EventCategory::BLOCK,
            EventCategory::OTHER,
            EventCategory::CUSTOM,
        ];
    }

    /**
     * Returns the category as key, and the associated events as values
     *
     * @return array
     */
    public function categoryEvents()
    {
        $map = [];
        foreach ($this->categories() as $category) {
            $map[$category] = $this->findByCategory($category);
        }

        return $map;
    }
}
