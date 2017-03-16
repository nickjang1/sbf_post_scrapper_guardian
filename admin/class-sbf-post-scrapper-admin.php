<?php
/**
 * The admin-specific functionality of the plugin.
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
require_once SBF_POST_SCRAPPER_DIR.'includes/class-sbf-post-scrapper-options.php';
require_once SBF_POST_SCRAPPER_DIR.'includes/tools/class-sbf-post-scrapper-manager.php';

class SBF_PostScrapper_Admin
{
    /**
     * The ID of this plugin.
     *
     * @var string The ID of this plugin
     */
    private $plugin_name;
    /**
     * The version of this plugin.
     *
     * @var string The current version of this plugin
     */
    private $version;
    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin
     * @param string $version     The version of this plugin
     */
    private $options;
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->options = new SBF_PostScrapper_Options();
    }
    /**
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, SBF_POST_SCRAPPER_URL.'admin/css/sbf-post-scrapper-admin.css', array(), $this->version, 'all');
    }
    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name, SBF_POST_SCRAPPER_URL.'admin/js/sbf-post-scrapper-admin.js', array('jquery'), $this->version, false);
    }

    public function register_menus()
    {
        $options_page = add_submenu_page('options-general.php', 'Softbladefor Post Scrapper', 'Post Scrap', 'manage_options', $this->options->option_group, array(&$this, 'option_page'));
    }

    public function register_settings()
    {
        register_setting($this->options->option_group, $this->options->slug);
    }

    public function option_page()
    {
        require_once SBF_POST_SCRAPPER_DIR.'admin/templates/options.php';
    }

    public function do_scrapping()
    {
        $scrapper_manager = new SBF_PostScrapper_Manager($this->options);
        $scrapper_manager->start();
        echo json_encode(array('success' => true));
        die;
    }
}
