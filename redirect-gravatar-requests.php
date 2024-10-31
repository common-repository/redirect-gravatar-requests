<?php
/**
 * Redirect Gravatar requests
 *
 * All requests to load an avatar from gravatar.com are redirected to a local image, preventing Gravatar from potentially gathering data about your site visitors.
 *
 * @package   Redirect_Gravatar_Requests
 * @author    Bart Kuijper
 * @copyright Copyright 2020 Bart Kuijper
 * @license   GPLv2 or later
 * @since     1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Redirect Gravatar requests
 * Plugin URI:        https://wordpress.org/plugins/redirect-gravatar-requests/
 * Description:       All requests to load an avatar from gravatar.com are redirected to a local image, preventing Gravatar from potentially gathering data about your site visitors.
 * Version:           2.0.0
 * Requires at least: 4.6
 * Tested up to:      6.0
 * Requires PHP:      5.6.20
 * Author:            Bart Kuijper
 * Author URI:        https://profiles.wordpress.org/spartelfant/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       redirect-gravatar-requests
 * Domain Path:       /languages
 */

// This is my namespace. There are many like it, but this one is mine.
namespace Bart_Kuijper\Redirect_Gravatar_Requests;

// Aborts in PhotonicInduction style if this file is called directly.
defined( 'ABSPATH' ) || die( 'Naughty&hellip; I ain&apos;t having it!' );

/**
 * Singleton class containing all plugin functionality.
 *
 * @since 1.0.0
 * @since 2.0.0 Class changed to final.
 */
final class Redirect_Gravatar_Requests {

	/**
	 * Holds reference to singleton instance of this class.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 Property changed from public to private.
	 * @var null|object $instance Assigned null upon declaration. Assigned a reference to the singleton instance of this property's class upon first instantiation of said class.
	 */
	private static $instance = null;

	/**
	 * Array containing all known Gravatars as keys for easy comparison. Some of these are redundant (mm, mystery, mysteryman) and one is not currently available as a choice (404), however all are listed as valid Gravatars at https://developer.wordpress.org/reference/functions/get_avatar/#parameters and would be retrieved from gravatar.com when selected.
	 *
	 * @since 1.0.0
	 * @var array $known_gravatars Array containing all known Gravatars as keys.
	 */
	private $known_gravatars = array(
		'404'              => null,
		'blank'            => null,
		'gravatar_default' => null,
		'identicon'        => null,
		'mm'               => null,
		'mystery'          => null,
		'mysteryman'       => null,
		'monsterid'        => null,
		'retro'            => null,
		'wavatar'          => null,
	);

	/**
	 * Returns a singleton instance of this class. Instantiates this class first if required.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 Refactored function.
	 *
	 * @return object Singleton instance of this class.
	 */
	public static function get_instance() {
		// Not nag me about the next line of code, PHPCS must.
		// phpcs:disable WordPress.PHP.YodaConditions.NotYoda, Squiz.PHP.DisallowMultipleAssignments.Found
		self::$instance === null && self::$instance = new self();
		// phpcs:enable WordPress.PHP.YodaConditions.NotYoda, Squiz.PHP.DisallowMultipleAssignments.Found
		return self::$instance;
	}

	/**
	 * Adds actions, filters and hooks upon class instantiation.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 Removed get_avatar_url() filter, added get_avatar() filter.
	 *
	 * @see get_instance()
	 */
	private function __construct() {
		// Delays loading of plugin translations until 'init' action.
		add_action( 'init', array( $this, 'load_own_textdomain' ) );

		// Filters get_avatar() to intercept avatar <img> tags with a Gravatar URL and replace those with our locally stored image.
		add_filter( 'get_avatar', array( $this, 'filter_get_avatar' ) );
		// Filters avatar_defaults() to remove Gravatars from default avatar selection.
		add_filter( 'avatar_defaults', array( $this, 'filter_avatar_defaults' ) );
		// Filters plugin_row_meta() to add links to this plugin's FAQ, Support, and Review pages on wordpress.org.
		add_filter( 'plugin_row_meta', array( $this, 'filter_plugin_row_meta' ), 10, 2 );

		// Registers functions that change the default avatar as required upon plugin (de)activation.
		register_activation_hook( __FILE__, array( $this, 'hook_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'hook_deactivation' ) );
	}

	/**
	 * Loads this plugin’s translated strings.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 Changed code used to acquire relative path.
	 *
	 * @link https://developer.wordpress.org/reference/functions/load_plugin_textdomain/
	 */
	public function load_own_textdomain() {
		load_plugin_textdomain( 'redirect-gravatar-requests', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Filters the <img> tag for the avatar returned by get_avatar(). If the tag's src attribute contains the string 'gravatar.com', this filter changes that attribute's value to point to this plugin's locally stored avatar.
	 *
	 * The previously filtered get_avatar_url() was easier to work with, since it contained nothing but the URL, while this filter contains a fully formed <img> tag. However some plugins ignore WP settings and implement their own code to show avatars (including Gravatars) anyway. The Simple History plugin does this for example. But at least that plugin also implements a call to apply_filters() for get_avatar(), giving us a chance to still intercept Gravatars.
	 *
	 * @since 2.0.0
	 *
	 * @link https://developer.wordpress.org/reference/hooks/get_avatar/
	 *
	 * @param string $img <img> tag for the user's avatar.
	 */
	public function filter_get_avatar( $img ) {
		// Perform a simple strpos() search to determine if there's a need to perform the more expensive regex search and replace. If a site has a lot of non-Gravatar avatars we don't want to bog it down by performing the regex for every single avatar.
		if ( ! strpos( $img, 'gravatar.com' ) === false ) {
			// Regex search for src='*gravatar.com*' or src="*gravatar.com*" (case insensitive) and replace with URL for locally stored image.
			$img = preg_replace( '/src=[\'"].+?gravatar\.com.+?[\'"]/i', 'src="' . plugin_dir_url( __FILE__ ) . 'mystery.jpg"', $img );
		}
		return $img;
	}

	/**
	 * Filters the default avatars in 'Settings -> Discussion -> Default Avatar' to replace all Gravatar options with our single locally stored image.
	 *
	 * @since 1.0.0
	 *
	 * @link https://developer.wordpress.org/reference/hooks/avatar_defaults/
	 *
	 * @param array $avatar_defaults Associative array of default avatars.
	 */
	public function filter_avatar_defaults( $avatar_defaults = array() ) {
		// Remove only the keys known to correspond to Gravatars in order to preserve any default avatars added by other plugins.
		$stripped_avatar_defaults = array_diff_key( $avatar_defaults, $this->known_gravatars );

		// The Gravatar key 'mystery' is then added again, both because that's the local image we're using as well as to have a valid saved setting here in case the plugin is deleted without first deactivating it. We're also tacking on some information. Note that there is nothing special about this 'mystery' key; it will still cause an attempt to load the 'Mystery Person' image from gravatar.com, but filter_get_avatar() is taking care of that.
		$stripped_avatar_defaults['mystery'] = sprintf( esc_html__( 'All Gravatar requests are being redirected to this locally stored image, regardless of this avatar being selected or not.', 'redirect-gravatar-requests' ) );

		// Return filtered list of default avatars.
		return $stripped_avatar_defaults;
	}

	/**
	 * Filters the array of row meta for each plugin in the Plugins list table.
	 *
	 * Adds links to this plugin's FAQ, Support, and Review pages on wordpress.org.
	 *
	 * @since 1.0.0
	 *
	 * @link https://developer.wordpress.org/reference/hooks/plugin_row_meta/
	 *
	 * @param array  $plugin_meta An array of the plugin's metadata, including the version, author, author URI, and plugin URI.
	 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
	 */
	public function filter_plugin_row_meta( $plugin_meta, $plugin_file ) {
		if ( plugin_basename( __FILE__ ) === $plugin_file ) {
			$meta        = array(
				'faq'     => '<a href="https://wordpress.org/plugins/redirect-gravatar-requests/#faq" target="_blank"><span class="dashicons dashicons-editor-help"></span> ' . esc_html__( 'FAQ', 'redirect-gravatar-requests' ) . '</a>',
				'support' => '<a href="https://wordpress.org/support/plugin/redirect-gravatar-requests/" target="_blank"><span class="dashicons dashicons-sos"></span> ' . esc_html__( 'Support', 'redirect-gravatar-requests' ) . '</a>',
				'review'  => '<a href="https://wordpress.org/support/plugin/redirect-gravatar-requests/reviews/#new-post" target="_blank"><span class="dashicons dashicons-star-filled"></span> ' . esc_html__( 'Review', 'redirect-gravatar-requests' ) . '</a>',
			);
			$plugin_meta = array_merge( $plugin_meta, $meta );
		}
		return $plugin_meta;
	}

	/**
	 * Handles plugin activation.
	 *
	 * If a Gravatar (or no avatar) was previously selected as default avatar, their removal by filter_avatar_defaults() would result in the Settings -> Discussion page displaying no default avatar being selected at all. That seems sloppy and could potentially confuse a user. So in that case the selection is changed to 'mystery', which we're using as a key for our locally stored image. This key is normally used for the 'Mystery Person' Gravatar, so it's unlikely to interfere with other avatar plugins. It also guarantees that if this plugin is deleted without being deactivated first, the Gravatar 'Mystery Person' is selected. Of course if a non-Gravatar avatar was previously selected, we do not change this setting. All neat and tidy ;)
	 *
	 * @since 1.0.0
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_activation_hook/
	 */
	public function hook_activation() {
		if ( array_key_exists( get_option( 'avatar_default', 'mystery' ), $this->known_gravatars ) ) {
			update_option( 'avatar_default', 'mystery' );
		}
	}

	/**
	 * Handles plugin deactivation.
	 *
	 * On plugin deactivation, if our locally stored 'mystery' avatar or no default avatar is selected, select the Gravatar logo to make it obvious to the user that Gravatars are active again. However if the default avatar is set to anything else, then there's probably another plugin adding default avatars, in which case we don't change the setting.
	 *
	 * @since 1.0.0
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_deactivation_hook/
	 */
	public function hook_deactivation() {
		if ( get_option( 'avatar_default', 'mystery' ) === 'mystery' ) {
			update_option( 'avatar_default', 'gravatar_default' );
		}
	}

	// End of class.
}

// Instantiate the plugin.
Redirect_Gravatar_Requests::get_instance();
