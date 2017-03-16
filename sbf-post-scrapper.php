<?php
/**
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://softbladefor.com
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Softbladefor Post Scrapper
 * Plugin URI:        http://softbladefor.com/wordpress/plugins/sbf-post-scrapper
 * Description:       wordpress post scrapping plugin
 * Version:           1.0.0
 * Author:            Arron Howard
 * Author URI:        http://softbladefor.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sbf-post-scrapper
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
define( 'SBF_POST_SCRAPPER_FILE', __FILE__ );
define( 'SBF_POST_SCRAPPER_DIR', plugin_dir_path( SBF_POST_SCRAPPER_FILE ) );
define( 'SBF_POST_SCRAPPER_UPLOAD_TEMP_DIR', wp_upload_dir().'/sbf-ps-tmp');
define( 'SBF_POST_SCRAPPER_URL', plugin_dir_url( SBF_POST_SCRAPPER_FILE ) );
define( 'SBF_POST_SCRAPPER_USER_AGENT', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36');

require_once SBF_POST_SCRAPPER_DIR.'includes/class-sbf-post-scrapper-activator.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php.
 */
function activate_sbf_post_scrapper()
{
    SBF_PostScrapper_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php.
 */
function deactivate_sbf_post_scrapper()
{
    SBF_PostScrapper_Deactivator::deactivate();
}
register_activation_hook(SBF_POST_SCRAPPER_FILE, 'activate_sbf_post_scrapper');
register_deactivation_hook(SBF_POST_SCRAPPER_FILE, 'deactivate_sbf_post_scrapper');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once SBF_POST_SCRAPPER_DIR.'includes/class-sbf-post-scrapper.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sbf_post_scrapper()
{
    $plugin = new SBF_PostScrapper();
    $plugin->run();
}
function abcdefg() {
    echo "asdf";
}
//add_action( 'wp_ajax_do_scrapping', 'abcdefg' );

run_sbf_post_scrapper();
