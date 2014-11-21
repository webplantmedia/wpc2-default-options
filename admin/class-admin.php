<?php
/**
 * WPC2 Default Options.
 *
 * @package   WPC2_Default_Options_Admin
 * @author    Chris Baldelomar <chris@webplantmedia.com>
 * @license   GPL-2.0+
 * @link      http://webplantmedia.com
 * @copyright 2014 Chris Baldelomar
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * @package   WPC2_Default_Options_Admin
 * @author  Chris Baldelomar <chris@webplantmedia.com>
 */
class WPC2_Default_Options_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	protected $plugin_slug = 'wpc2-default-options';
	protected $plugin_prefix = 'wpc2_default_options';

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	const VERSION = '1.0';

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		define( 'WPC2_DEFAULT_OPTIONS_IS_ACTIVATED', true );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return the plugin prefix.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_prefix() {
		return $this->plugin_prefix;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), self::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @TODO:
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * @TODO:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_submenu_page(
			'tools.php',
			__( 'Default Options', 'wpc2-default-options' ),
			__( 'Default Options', 'wpc2-default-options' ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'tools.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', 'wpc2-default-options' ) . '</a>'
			),
			$links
		);

	}

	private function check_for_local_development( $template ) {
		if ( 'wpcanvas2' == $template ) {
			$theme = wp_get_theme( $template );

			switch ( $theme->Name ) {
				case 'Meadowbrook - Premium' :
					return 'meadowbrook-premium';
					break;
			}
		}

		return $template;
	}

	private function get_remote_default_options_php() {
		if ( ! defined( 'GITHUB_TOKEN' ) || ! defined( 'GITHUB_URL' ) ) {
			return "Need to define constant GITHUB_TOKEN and GITHUB_URL in your config.php file";
		}

		$template = get_template();
		$template = $this->check_for_local_development( $template );

		$url = GITHUB_URL . '?ref=' . $template;

		$options = array(
			'method' => 'GET',
			'headers' => array(
				'Authorization' => 'token ' . GITHUB_TOKEN,
				'Accept' => 'application/vnd.github.v3.raw',
			),
			'sslverify' => false,
			'sslcertificates' => '',
		);

		$raw_response = wp_remote_post( $url, $options );

		if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) )
			return "Could not get file";

		$response = wp_remote_retrieve_body( $raw_response );

		return $response;
	}

	private function get_default_options_php() {
		$template_path = get_template_directory();
		$file = $template_path . "/inc/default-options.php";

		if ( ! is_file( $file ) )
			return "File '/inc/default-options.php' does not exist";

		$content = '';
		if ( filesize( $file ) > 0 ) {
			$f = fopen($file, 'r');
			$content = fread($f, filesize($file));
			fclose( $f );
		}

		return $content;
	}

	private function normalize($s) {
		// Normalize line endings
		// Convert all line-endings to UNIX format
		$s = str_replace("\r\n", "\n", $s);
		$s = str_replace("\r", "\n", $s);
		// Don't allow out-of-control blank lines
		$s = preg_replace("/\n{2,}/", "\n\n", $s);

		return $s;
	}

	private function search_replace_customizer_options( $file ) {
		global $wpc2_default;
		$download = array();

		if ( ! $mods = get_theme_mods() ) {
			return $file;
		}

		$uri = get_template_directory_uri();
		$uri_esc = preg_quote( $uri, '/' );
		$at_font_face = preg_quote( '@font-face', '/' );

		foreach ( $wpc2_default as $key => $value ) {
			if ( array_key_exists( $key, $mods ) ) {
				$value = $mods[ $key ];
			}

			if ( 'custom_css' == $key ) {
				if ( ! empty( $value ) ) {
					echo '<h4><strong>TODO</strong>: Manage Custom CSS</h4>';
					echo '<pre>'.$value.'</pre>';
				}
				continue;
			}

			if ( 'favicon' == $key ) {
				echo '<h4><strong>NOTICE</strong>: Not saving favicon image in default options</h4>';
				echo '<pre>'.$value.'</pre>';
				continue;
			}

			$value = "'" . $value . "'";

			if ( preg_match( '/'.$at_font_face.'.*/', $value ) ) {
				if ( preg_match_all( '/(https?\:\/\/.*?)\.(woff|woff2|eot|ttf|svg)/s', $value, $matches ) ) {
					if ( isset( $matches[0] ) && ! empty( $matches[0] ) ) {
						$matches = array_unique( $matches[0] );
						$download[] = array(
							'key' => $key,
							'newname' => '',
							'url' => $matches,
						);
					}
				}

				$value = str_replace( "'".$uri, "'\" . get_template_directory_uri() . \"", $value );
				$value = trim( $value, "'" );
				$value = "trim(\"\n" . $value . "\n\")";
				// remove any @font-face rules so we can easily search and replace
				$file = preg_replace( '/^\$wpc2\_default\[\'' . preg_quote( $key, '/' ) . '\'\].*$/m', '$wpc2_default[\'' . $key . '\'] = "";', $file );
				$file = preg_replace( '/^\@font-face\s\{.*?"\)\;[\\n|\\r\\n]/ms', '', $file );

			}
			else if ( preg_match( '/'.$uri_esc.'.*/', $value ) ) {
				$value = 'get_template_directory_uri() . ' . str_replace( $uri, '', $value );
			}
			else if ( preg_match( '/(https?\:\/\/.*?)\.(jpe?g|png|gif|bmp)/', $value, $matches ) ) {
				if ( isset( $matches[0] ) && ! empty( $matches[0] ) ) {
					$image = $matches[0];

					$pathinfo = pathinfo( $image );
					$newname = str_replace( '_', '-', $key ) . '.' . $pathinfo['extension'];
					$value = 'get_template_directory_uri() . \'/img/' . $newname . '\'';

					$download[] = array(
						'key' => $key,
						'newname' => $newname,
						'url' => $matches[0],
					);
				}
			}

			if ( $value != strip_tags( $value ) ) {
				$value = htmlspecialchars( $value );
			}

			$file = preg_replace( '/^\$wpc2\_default\[\'' . preg_quote( $key, '/' ) . '\'\].*$/m', '$wpc2_default[\'' . $key . '\'] = '.$value.';', $file );
		}

		include_once( 'views/download-media.php' );

		$file = $this->normalize( $file );

		return trim( $file );
	}
	
	public function display_default_options_php() {
		global $wpc2_default;

		if ( ! $mods = get_theme_mods() ) {
			echo '<p>No Data</p>';
			return;
		}

		$uri = get_template_directory_uri();
		$uri_esc = preg_quote( $uri, '/' );

		$file = $this->get_default_options_php();

		$file = $this->search_replace_customizer_options( $file );

		include_once( 'views/default-options.php' );
	}
	
	public function display_customizer_options() {
		global $wpc2_default;

		if ( ! $mods = get_theme_mods() ) {
			echo '<p>No Data</p>';
			return;
		}

		$uri = get_template_directory_uri();
		$uri_esc = preg_quote( $uri, '/' );
		$at_font_face = preg_quote( '@font-face', '/' );

		echo '<pre>';
		foreach ( $wpc2_default as $key => $value ) {
			if ( array_key_exists( $key, $mods ) ) {
				$value = $mods[ $key ];
			}

			$value = "'" . $value . "'";

			if ( preg_match( '/'.$at_font_face.'.*/', $value ) ) {
				// $value = preg_replace( "/(['|\"])".preg_quote( $uri, '/' )."/", 'get_template_directory_uri() . \\1', $value );
				$value = str_replace( "'".$uri, "'\" . get_template_directory_uri() . \"", $value );
				$value = trim( $value, "'" );
				$value = "trim(\"\n" . $value . "\n\")";
			}
			else if ( preg_match( '/'.$uri_esc.'.*/', $value ) ) {
				$value = 'get_template_directory_uri() . ' . str_replace( $uri, '', $value );
			}

			if ( $value != strip_tags( $value ) ) {
				$value = htmlspecialchars( $value );
			}

			echo '$wpc2_default[\'' . $key . '\'] = '.$value.';<br />';
		}
		echo '</pre>';
	}
	
	public function restore_default_options() {
		global $wpc2_default;
		$restored = false;

		if ( ! $mods = get_theme_mods() ) {
			echo '<p>No Data</p>';
			return;
		}

		echo '<p>The following options have been restored:</p>';
		echo '<p>';
		foreach ( $wpc2_default as $key => $value ) {
			if ( array_key_exists( $key, $mods ) ) {
				remove_theme_mod( $key );
				echo $key . '<br />';
				$restored = true;
			}
		}
		if ( ! $restored ) {
			echo "No options to restore.";
		}
		echo '</p>';
	}
}
