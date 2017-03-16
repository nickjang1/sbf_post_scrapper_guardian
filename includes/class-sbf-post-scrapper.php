<?php
/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class SBF_PostScrapper
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     */
    protected $loader;
    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     *
     * @var string The string used to uniquely identify this plugin
     */
    protected $plugin_name;
    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     *
     * @var string The current version of the plugin
     */
    protected $version;
    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        $this->plugin_name = 'sbf-post-scrapper';
        $this->version = '1.0.0';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }
    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - SBF_PostScrapper_Loader. Orchestrates the hooks of the plugin.
     * - SBF_PostScrapper_i18n. Defines internationalization functionality.
     * - SBF_PostScrapper_Admin. Defines all hooks for the admin area.
     * - SBF_PostScrapper_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once SBF_POST_SCRAPPER_DIR.'includes/class-sbf-post-scrapper-loader.php';
        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once SBF_POST_SCRAPPER_DIR.'includes/class-sbf-post-scrapper-i18n.php';
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once SBF_POST_SCRAPPER_DIR.'admin/class-sbf-post-scrapper-admin.php';
        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once SBF_POST_SCRAPPER_DIR.'public/class-sbf-post-scrapper-public.php';
        $this->loader = new SBF_PostScrapper_Loader();
    }
    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the SBF_PostScrapper_i18n class in order to set the domain and to register the hook
     * with WordPress.
     */
    private function set_locale()
    {
        $plugin_i18n = new SBF_PostScrapper_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }
    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new SBF_PostScrapper_Admin($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'register_menus');
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
        // $this->loader->add_action('admin_menu', $plugin_admin, 'do_scrapping');
        $this->loader->add_action('wp_ajax_do_scrapping', $plugin_admin, 'do_scrapping');
    }
    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     */
    private function define_public_hooks()
    {
        $plugin_public = new SBF_PostScrapper_Public($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }
    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }
    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     *
     * @return string The name of the plugin
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }
    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     *
     * @return SBF_PostScrapper_Loader Orchestrates the hooks of the plugin
     */
    public function get_loader()
    {
        return $this->loader;
    }
    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     *
     * @return string The version number of the plugin
     */
    public function get_version()
    {
        return $this->version;
    }
}
