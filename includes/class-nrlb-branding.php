<?php
/**
 * Neuralab Branding setup.
 */

defined( 'ABSPATH' ) || exit;

/**
 * NRLB_Branding class.
 */
final class NRLB_Branding {
  /**
   * Instance of the current class, null before first usage.
   *
   * @var NRLB_Branding
   */
  protected static $instance = null;

  /**
   * Return class instance.
   *
   * @return NRLB_Branding
   */
  public static function get_instance() {
    if ( is_null( self::$instance ) ) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  /**
   * Cloning is forbidden.
   */
  public function __clone() {
    return wp_die( 'Cloning is forbidden!' );
  }

  /**
   * Unserializing instances of this class is forbidden.
   */
  public function __wakeup() {
    return wp_die( 'Unserializing instances is forbidden!' );
  }

  /**
   * Class construct.
   */
  private function __construct() {
    $this->define_constants();
    $this->includes();
    $this->init_hooks();
    $this->update();
  }

  /**
   * Define plugin constants.
   */
  private function define_constants() {
    if ( ! defined( 'NRLB_BRANDING_DIR_PATH' ) ) {
      define( 'NRLB_BRANDING_DIR_PATH', plugin_dir_path( NRLB_BRANDING_ROOT_FILE ) );
    }

    if ( ! defined( 'NRLB_BRANDING_DIR_URL' ) ) {
      define( 'NRLB_BRANDING_DIR_URL', plugin_dir_url( NRLB_BRANDING_ROOT_FILE ) );
    }
  }

  /**
   * Add includes.
   *
   * To require a file add require_once NRLB_BRANDING_DIR_PATH . 'includes/path/to/file.php';
   */
  private function includes() {
    if ( file_exists( NRLB_BRANDING_DIR_PATH . 'vendor/autoload.php' ) ) {
      require_once NRLB_BRANDING_DIR_PATH . 'vendor/autoload.php';
    }
  }

  /**
   * Init plugin hooks.
   */
  private function init_hooks() {
    // Activation and deactivation hooks.
    register_activation_hook( NRLB_BRANDING_ROOT_FILE, [ $this, 'activate' ] );
    register_deactivation_hook( NRLB_BRANDING_ROOT_FILE, [ $this, 'deactivate' ] );

    // Load text domain.
    add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );

    // Plugin hooks.
    // Admin branding.
    add_filter( 'admin_bar_menu', [ $this, 'remove_wp_logo_from_admin_bar' ], 100 );
    add_action( 'admin_bar_menu', [ $this, 'admin_bar_menu' ], 1 );
    add_action( 'wp_before_admin_bar_render', [ $this, 'style_admin_bar_menu' ] );
    add_filter( 'admin_footer_text', [ $this, 'admin_footer_text' ], PHP_INT_MAX );
    // Login branding.
    add_filter( 'login_headerurl', [ $this, 'login_header_url' ], 100 );
    add_filter( 'login_headertext', [ $this, 'login_header_text' ], 100 );
    add_action( 'login_enqueue_scripts', [ $this, 'login_logo_style' ], PHP_INT_MAX );
  }

  /**
   * Activate plugin.
   */
  public function activate() {
    if ( ! current_user_can( 'activate_plugins' ) ) {
      return false;
    }
  }

  /**
   * Deactivate plugin.
   */
  public function deactivate() {
    if ( ! current_user_can( 'activate_plugins' ) ) {
      return false;
    }
  }

  /**
   * Load text domain.
   */
  public function load_textdomain() {
    load_plugin_textdomain( 'nrlb-branding', false, dirname( plugin_basename( NRLB_BRANDING_ROOT_FILE ) ) . '/languages/' );
  }

  /**
   * Enable plugin updates from BitBucket repository.
   */
  public function update() {
    $update = Puc_v4_Factory::buildUpdateChecker(
      'https://bitbucket.org/neuralab/neuralab-branding',
      NRLB_BRANDING_ROOT_FILE,
      'nrlb-branding'
    );

    $update->setAuthentication(
      [
        'consumer_key'    => '***REMOVED***',
        'consumer_secret' => '***REMOVED***',
      ]
    );
  }

  /**
   * Get Neuralab site URL.
   *
   * @return string
   */
  public function get_nrlb_url() {
    return 'https://www.neuralab.net';
  }

  /**
   * Get Neuralab site copy.
   *
   * @return string
   */
  public function get_nrlb_url_copy() {
    return __( 'a.neuralab.site', 'nrlb-branding' );
  }

  /**
   * Get Neuralab site link.
   *
   * @return string
   */
  public function a_nrlb_site() {
    $url  = $this->get_nrlb_url();
    $copy = $this->get_nrlb_url_copy();

    return apply_filters( 'nrlb_branding_a_nrlb_site', '<a class="a-nrlb-site" target="_blank" rel="noopener" href="' . esc_url( $url ) . '">' . esc_html( $copy ) . '</a>', $url, $copy );
  }

  /**
   * Remove WordPress logo from admin bar.
   *
   * @param  object $wp_admin_bar WP_Admin_Bar object.
   */
  public function remove_wp_logo_from_admin_bar( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'wp-logo' );
  }

  /**
   * Add admin bar menu.
   */
  public function admin_bar_menu() {
    global $wp_admin_bar;

    $wp_admin_bar->add_node(
      [
        'id'    => 'nrlb-logo',
        'title' => '<span class="ab-icon">' . file_get_contents( NRLB_BRANDING_DIR_PATH . '/assets/img/neuralab-logo-sm.svg' ) . '</span><span class="screen-reader-text">' . esc_html( $this->get_nrlb_url_copy() ) . '</span>', // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
        'href'  => esc_url( $this->get_nrlb_url() ),
        'meta'  => [
          'target' => '_blank',
          'rel'    => 'noopener',
        ],
      ]
    );
  }

  /**
   * Admin bar menu CSS.
   */
  public function style_admin_bar_menu() {
    ?>
    <style type="text/css">
      #wpadminbar #wp-admin-bar-nrlb-logo > .ab-item .ab-icon {
        margin-right: 0 !important;
      }
      #wpadminbar #wp-admin-bar-nrlb-logo > .ab-item .ab-icon svg {
        position: relative;
        top: 2px;
        display: block;
        float: left;
        width:  20px;
        height: 20px;
        fill: currentColor;
      }
    </style>
    <?php
  }

  /**
   * Edit admin footer text.
   *
   * @param  string $text
   * @return string
   */
  public function admin_footer_text( $text ) {
    return '<span id="footer-thankyou"><a href="' . esc_url( $this->get_nrlb_url() ) . '" target="_blank" rel="noopener">' . esc_html( $this->get_nrlb_url_copy() ) . '</a></span>';
  }

  /**
   * Edit login header URL.
   *
   * @param  string $login_header_url
   * @return string
   */
  public function login_header_url( $login_header_url ) {
    return esc_url( $this->get_nrlb_url() );
  }

  /**
   * Login header text.
   *
   * @param  string $login_header_text
   * @return string
   */
  public function login_header_text( $login_header_text ) {
    return esc_html( $this->get_nrlb_url_copy() );
  }


  /**
   * Login logo CSS.
   */
  public function login_logo_style() {
    ?>
    <style type="text/css">
      .login h1 a {
      background-image: url( <?php echo NRLB_BRANDING_DIR_URL . '/assets/img/neuralab-logo.svg'; ?> ) !important;
      background-repeat: no-repeat !important;
      background-size: 300px 40px !important;
      background-position: center !important;
      width: 100% !important;
    }
    </style>
    <?php
  }
}
