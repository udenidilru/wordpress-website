<?php

/**
 * Suppress "error - 0 - No summary was found for this file" on phpdoc generation
 *
 * @package plugin\includes
 */

use WPDataAccess\Connection\WPDADB;
use WPDataAccess\Cookies\WPDA_Cookies;
use WPDataAccess\Data_Dictionary\WPDA_Dictionary_Lists;
use WPDataAccess\Data_Tables\WPDA_Data_Tables;
use WPDataAccess\Utilities\WPDA_Table_Actions;
use WPDataAccess\Utilities\WPDA_Export;
use WPDataAccess\Utilities\WPDA_Favourites;
use WPDataAccess\WPDA;
use WPDataProjects\Utilities\WPDP_Export_Project;
use WPDataAccess\Backup\WPDA_Data_Export;
use WPDataAccess\Settings\WPDA_Settings;
use WPDataAccess\Plugin_Table_Models\WPDA_CSV_Uploads_Model;
use WPDataRoles\WPDA_Roles;

/**
 * Class WP_Data_Access
 *
 * Core plugin class used to define:
 * + admin specific functionality {@see WP_Data_Access_Admin}
 * + public specific functionality {@see WP_Data_Access_Public}
 * + internationalization {@see WP_Data_Access_i18n}
 * + plugin activation and deactivation {@see WP_Data_Access_Loader}
 *
 * @author  Peter Schulz
 * @since   1.0.0
 *
 * @see WP_Data_Access_Admin
 * @see WP_Data_Access_Public
 * @see WP_Data_Access_i18n
 * @see WP_Data_Access_Loader
 */
class WP_Data_Access {

	/**
	 * Reference to plugin loader
	 *
	 * @var WP_Data_Access_Loader
	 *
	 * @since   1.0.0
	 *
	 * @see WP_Data_Access_Loader
	 */
	protected $loader;

	/**
	 * WP_Data_Access constructor
	 *
	 * Calls method the following methods to setup plugin:
	 * + {@see WP_Data_Access::load_dependencies()}
	 * + {@see WP_Data_Access::set_locale()}
	 * + {@see WP_Data_Access::define_admin_hooks()}
	 * + {@see WP_Data_Access::define_public_hooks()}
	 *
	 * @since   1.0.0
	 *
	 * @see WP_Data_Access::load_dependencies()
	 * @see WP_Data_Access::set_locale()
	 * @see WP_Data_Access::define_admin_hooks()
	 * @see WP_Data_Access::define_public_hooks()
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load required dependencies
	 *
	 * Loads required plugin files and initiates the plugin loader.
	 *
	 * @since   1.0.0
	 *
	 * @see WP_Data_Access_Loader
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-data-access-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-data-access-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-data-access-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-data-access-public.php';

		$this->loader = new WP_Data_Access_Loader();
	}

	/**
	 * Set locale for internationalization
	 *
	 * @since   1.0.0
	 *
	 * @see WP_Data_Access_i18n
	 */
	private function set_locale() {
		$wpda_i18n = new WP_Data_Access_i18n();
		$this->loader->add_action( 'init', $wpda_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Add admin hooks
	 *
	 * Initiates {@see WP_Data_Access_Admin} (admin functionality) and {@see WPDA_Export} (export functionality).
	 * Adds the appropriate actions to the loader.
	 *
	 * @since   1.0.0
	 *
	 * @see WP_Data_Access_Admin
	 * @see WPDA_Export
	 */
	private function define_admin_hooks() {
		$plugin_admin = new WP_Data_Access_Admin();

		// Handle plugin cookies.
		$wpda_cookies = new WPDA_Cookies();
		$this->loader->add_action( 'admin_init', $wpda_cookies, 'handle_plugin_cookies' );

		// Admin menu.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_items' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_my_tables', 11 );

		// Admin scripts.
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Add settings page
		$this->loader->add_action('admin_menu', $this, 'wpdataaccess_register_settings_page');

		// Export action.
		$plugin_export = new WPDA_Export();
		$this->loader->add_action( 'admin_action_wpda_export', $plugin_export, 'export' );

		// Add/remove favourites.
		$plugin_favourites = new WPDA_Favourites();
		$this->loader->add_action( 'admin_action_wpda_add_favourite', $plugin_favourites, 'add' );
		$this->loader->add_action( 'admin_action_wpda_rem_favourite', $plugin_favourites, 'rem' );

		// Show tables actions.
		$plugin_table_actions = new WPDA_Table_Actions();
		$this->loader->add_action( 'admin_action_wpda_show_table_actions', $plugin_table_actions, 'show' );

		$plugin_dictionary_list = new WPDA_Dictionary_Lists();
		// Get tables for a specific database.
		$this->loader->add_action( 'admin_action_wpda_get_tables', $plugin_dictionary_list, 'get_tables_ajax' );
		// Get columns for a specific table.
		$this->loader->add_action( 'admin_action_wpda_get_columns', $plugin_dictionary_list, 'get_columns' );

		// Export project.
		$plugin_export_project = new WPDP_Export_Project();
		$this->loader->add_action( 'admin_action_wpda_export_project', $plugin_export_project, 'export' );

		// Data backup.
		$wpda_data_backup = new WPDA_Data_Export();
		$this->loader->add_action( 'wpda_data_backup', $wpda_data_backup, 'wpda_data_backup' );

		// Allow to add multiple user roles
		$wpda_roles = new WPDA_Roles();
		$this->loader->add_action( 'user_new_form', $wpda_roles, 'multiple_roles_selection' );
		$this->loader->add_action( 'edit_user_profile', $wpda_roles, 'multiple_roles_selection' );
		$this->loader->add_action( 'profile_update', $wpda_roles, 'multiple_roles_update' );
		$this->loader->add_filter( 'manage_users_columns', $wpda_roles, 'multiple_roles_label' );

		// Check if a remote db connection can be established via ajax
		$wpdadb = new WPDADB();
		$this->loader->add_action( 'admin_action_wpda_check_remote_database_connection', $wpdadb, 'check_remote_database_connection' );

		// Add id to wpda_datatables.js (for IE)
		$this->loader->add_filter( 'script_loader_tag', $this, 'add_id_to_script', 10, 3 );

		// Add CSV mapping calls
		$wpda_csv_uploads_model = new WPDA_CSV_Uploads_Model();
		$this->loader->add_action( 'admin_action_wpda_save_csv_mapping', $wpda_csv_uploads_model, 'save_mapping' );
		$this->loader->add_action( 'admin_action_wpda_csv_preview_mapping', $wpda_csv_uploads_model, 'preview_mapping' );

		// Show what's new page and update option
		add_action(
			'admin_action_wpda_show_whats_new',
			function() {
				if ( isset( $_REQUEST['whats_new'] ) && 'off' === $_REQUEST['whats_new'] ) {
					WPDA::set_option( WPDA::OPTION_WPDA_SHOW_WHATS_NEW, 'off' );
				}
				header('Location: https://wpdataaccess.com/docs/documentation/updates/whats-new/');
			},
			10,
			1
		);

		if ( wpda_fremius()->is__premium_only() ) {
			// ADD FULL TEXT SEARCH

			// Admin menu
			$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_items' );

			// Admin scripts
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

			// Add full-text search support
			// Add index actions to Data Explorer main page > table settings
			$wpdapro_search = new WPDataAccess\Premium\WPDAPRO_FullText_Search\WPDAPRO_Search();
			$this->loader->add_action( 'admin_action_wpdapro_create_fulltext_index', $wpdapro_search, 'create_fulltext_index' );
			$this->loader->add_action( 'admin_action_wpdapro_check_fulltext_index', $wpdapro_search, 'check_fulltext_index' );
			$this->loader->add_action( 'admin_action_wpdapro_cancel_fulltext_index', $wpdapro_search, 'cancel_fulltext_index' );
			$this->loader->add_action( 'admin_action_wpdapro_no_listbox_items', $wpdapro_search, 'no_listbox_items' );
			// Add settings to Data Explorer main page > table settings
			$this->loader->add_action( 'wpda_prepend_table_settings', $wpdapro_search, 'add_search_settings', 10, 3 );
			// Add search actions to Data Explorer table page search box
			$this->loader->add_action( 'wpda_add_search_actions', $wpdapro_search, 'add_search_actions', 10, 4 );
			// Add search html to Data Explorer table page search box
			$this->loader->add_action( 'wpda_add_search_filter', $wpdapro_search, 'add_search_filter', 10, 4 );
			// Add individual column search to publication (creates jQuery DataTable search columns)
			$this->loader->add_filter( 'wpda_wpdataaccess_prepare', $wpdapro_search, 'add_column_search_to_publication', 10, 6 );
			// Add pro search to list tables
			$this->loader->add_filter( 'wpda_construct_where_clause', $wpdapro_search, 'construct_where_clause', 10, 5 );

			// ADD INLINE EDITING

			// Admin menu
			$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_items' );
			add_action(
				'admin_enqueue_scripts', function () {
					if ( ! current_user_can( 'manage_options' ) ) {
						// Remove freemius support menu for non admin users
						remove_submenu_page( 'wpda', 'wpda-wp-support-forum' );
					}
				}
			);

			// Admin scripts
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

			// Add line editing support
			// Add inline editing to list tables
			$inline_editing = new WPDataAccess\Premium\WPDAPRO_Inline_Editing\WPDAPRO_Inline_Editing();
			$this->loader->add_filter( 'wpda_column_default', $inline_editing, 'add_inline_editing_to_column', 10, 8 );
			// Add ajax call to inline editing
			$this->loader->add_action( 'admin_action_wpdapro_inline_editor', $inline_editing, 'wpdapro_inline_editor', 10, 7 );
			// Add inline editing column selection to Data Explorer settings
			add_filter(
				'wpda_add_column_settings',
				function( $column_settings ) {
					$add_column = [
						[
							'label'        => 'Edit',
							'hint'         => 'Allow inline editing?',
							'name_prefix'  => 'inline_editing_',
							'type'         => 'checkbox',
							'default'      => '',
							'disable'      => 'keys',
						]
					];

					if ( is_array( $column_settings ) ) {
						array_push( $column_settings, $add_column[0] );
						return $column_settings;
					} else {
						return $add_column;
					}
				},
				10,
				2
			);
			// Add inline editing to shortcode wpdadiehard
			add_action(
				'wpda_wpdadiehard_prepare',
				function() {
					wp_enqueue_style( 'wpdapro_inline_editing' );
					wp_enqueue_script( 'wpdapro_inline_editor' );
				},
				10,
				1
			);
			// Add inline editing settings to Data Projects
			$inline_editing_projects = new WPDataAccess\Premium\WPDAPRO_Inline_Editing\WPDAPRO_Data_Projects();
			$this->loader->add_action(
				'wpda_data_projects_add_table_option',
				$inline_editing_projects,
				'add_table_option',
				10,
				2
			);
			// Save Data Projects inline editing settings
			$this->loader->add_filter(
				'wpda_data_projects_save_table_option',
				$inline_editing_projects,
				'save_table_option',
				10,
				3
			);
		}
	}

	public function add_id_to_script( $tag, $handle, $src ) {
		if ( 'wpda_datatables' === $handle ) {
			$tag = '<script id="wpda_datatables" src="' . $src . '"></script>';
		}
		return $tag;
	}

	/**
	 * Add public hooks
	 *
	 * Initiates {@see WP_Data_Access_Public} (public functionality), {@see WPDA_Data_Tables} (ajax call to support
	 * server side jQuery DataTables functionality). Adds the appropriate actions to
	 * the loader.
	 *
	 * @since   1.0.0
	 *
	 * @see WP_Data_Access_Public
	 * @see WPDA_Data_Tables
	 * @see WPDA_Dictionary_Lists
	 */
	private function define_public_hooks() {
		$plugin_public = new WP_Data_Access_Public();

		// Handle plugin cookies.
		$wpda_cookies = new WPDA_Cookies();
		$this->loader->add_action( 'init', $wpda_cookies, 'handle_plugin_cookies' );

		// Shortcodes.
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );

		// Public scripts.
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Ajax calls.
		$plugin_datatables = new WPDA_Data_Tables();
		$this->loader->add_action( 'wp_ajax_wpda_datatables', $plugin_datatables, 'get_data' );
		$this->loader->add_action( 'wp_ajax_nopriv_wpda_datatables', $plugin_datatables, 'get_data' );

		// Export action.
		$plugin_export = new WPDA_Export();
		$this->loader->add_action( 'wp_ajax_wpda_export', $plugin_export, 'export' );
		$this->loader->add_action( 'wp_ajax_nopriv_wpda_export', $plugin_export, 'export' );

		// Add id to wpda_datatables.js (for IE)
		$this->loader->add_filter( 'script_loader_tag', $this, 'add_id_to_script', 10, 3 );

		if ( wpda_fremius()->is__premium_only() ) {
			// ADD INLINE EDITING
			$inline_editing = new WPDataAccess\Premium\WPDAPRO_Inline_Editing\WPDAPRO_Inline_Editing();
			$this->loader->add_action( 'wp_ajax_wpdapro_inline_editor', $inline_editing, 'wpdapro_inline_editor', 10, 7 );
			$this->loader->add_action( 'wp_ajax_nopriv_wpdapro_inline_editor', $inline_editing, 'wpdapro_inline_editor', 10, 7 );

			// Initialize Grid Editor
			$grid_editor = new WPDataAccess\Premium\WPDAPRO_Grid_Editor\WPDAPRO_Grid_Editor_Actions();

			$this->loader->add_action( 'wp_ajax_wpdapro_datagrid_get_headers', $grid_editor, 'get_headers', 10, 1 );
			$this->loader->add_action( 'wp_ajax_nopriv_wpdapro_datagrid_get_headers', $grid_editor, 'get_headers', 10, 1 );

			$this->loader->add_action( 'wp_ajax_wpdapro_datagrid_get_records', $grid_editor, 'get_records', 10, 1 );
			$this->loader->add_action( 'wp_ajax_nopriv_wpdapro_datagrid_get_records', $grid_editor, 'get_records', 10, 1 );

			$this->loader->add_action( 'wp_ajax_wpdapro_datagrid_create_record', $grid_editor, 'create_record', 10, 2 );
			$this->loader->add_action( 'wp_ajax_nopriv_wpdapro_datagrid_create_record', $grid_editor, 'create_record', 10, 2 );

			$this->loader->add_action( 'wp_ajax_wpdapro_datagrid_update_record', $grid_editor, 'update_record', 10, 2 );
			$this->loader->add_action( 'wp_ajax_nopriv_wpdapro_datagrid_update_record', $grid_editor, 'update_record', 10, 2 );

			$this->loader->add_action( 'wp_ajax_wpdapro_datagrid_delete_record', $grid_editor, 'delete_record', 10, 2 );
			$this->loader->add_action( 'wp_ajax_nopriv_wpdapro_datagrid_delete_record', $grid_editor, 'delete_record', 10, 2 );
		}
	}

	/**
	 * Start plugin loader
	 *
	 * @since   1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Add plugin settings page
	 */
	public function wpdataaccess_register_settings_page() {
		add_options_page(
			'WP Data Access',
			'WP Data Access',
			'manage_options',
			WP_Data_Access_Admin::PAGE_SETTINGS,
			[
				$this,
				'wpdataaccess_settings_page'
			]
		);
	}

	/**
	 * Show settings page
	 */
	public function wpdataaccess_settings_page() {
		$wpda_settings = new WPDA_Settings();
		$wpda_settings->show();
	}

}
