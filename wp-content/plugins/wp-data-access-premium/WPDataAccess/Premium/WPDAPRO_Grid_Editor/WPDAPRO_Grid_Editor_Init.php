<?php

/**
 * Suppress "error - 0 - No summary was found for this file" on phpdoc generation
 *
 * @package WPDataAccess\Plugin_Table_Models
 */

namespace WPDataAccess\Premium\WPDAPRO_Grid_Editor {

	use WPDataAccess\WPDA;

	/**
	 * Class WPDA_Plugin_Table_Base_Model
	 *
	 * Model for plugin table 'wpda_datagrid'
	 *
	 * @author  Max Schulz
	 * @since   3.5.0
	 */
	class WPDAPRO_Grid_Editor_Init {

		const slickgrid_styles = [
			'wpdapro_grid_editor_wpda_datagrid'               => 'datagrid/datagrid.css',
			'wpdapro_grid_editor_slick_grid'                  => 'slickgrid/slick.grid.css',
			'wpdapro_grid_editor_slick_default_theme'         => 'slickgrid/slick-default-theme.css',
			'wpdapro_grid_editor_controls_slick_columnpicker' => 'slickgrid/controls/slick.columnpicker.css',
			'wpdapro_grid_editor_controls_slick_gridmenu'     => 'slickgrid/controls/slick.gridmenu.css',
			'wpdapro_grid_editor_controls_slick_pager'        => 'slickgrid/controls/slick.pager.css',
			'wpdapro_grid_editor_css_slick_smoothness'        => 'slickgrid/css/smoothness/jquery-ui-1.11.3.custom.min.css',
			'wpdapro_grid_editor_plugins_slick_cellmenu'      => 'slickgrid/plugins/slick.cellmenu.css',
			'wpdapro_grid_editor_plugins_slick_contextmenu'   => 'slickgrid/plugins/slick.contextmenu.css',
			'wpdapro_grid_editor_plugins_slick_headerbuttons' => 'slickgrid/plugins/slick.headerbuttons.css',
			'wpdapro_grid_editor_plugins_slick_headermenu'    => 'slickgrid/plugins/slick.headermenu.css',
			'wpdapro_grid_editor_plugins_slick_rowdetailview' => 'slickgrid/plugins/slick.rowdetailview.css',
		];

		const slickgrid_scripts = [
			'wpdapro_grid_editor_wpda_ajax_calls'                       => 'datagrid/ajax_calls.js',
			'wpdapro_grid_editor_wpda_datagrid'                         => 'datagrid/datagrid.js',
			'wpdapro_grid_editor_wpda_editor'                           => 'datagrid/editor.js',
			'wpdapro_grid_editor_wpda_jquery_event_drag'                => 'datagrid/jquery.event.drag-2.3.0.js',
			'wpdapro_grid_editor_slick_core'                            => 'slickgrid/slick.core.js',
			'wpdapro_grid_editor_slick_dataview'                        => 'slickgrid/slick.dataview.js',
			'wpdapro_grid_editor_slick_editors'                         => 'slickgrid/slick.editors.js',
			'wpdapro_grid_editor_slick_formatters'                      => 'slickgrid/slick.formatters.js',
			'wpdapro_grid_editor_slick_grid'                            => 'slickgrid/slick.grid.js',
			'wpdapro_grid_editor_slick_groupitemmetadataprovider'       => 'slickgrid/slick.groupitemmetadataprovider.js',
			'wpdapro_grid_editor_slick_remotemodel'                     => 'slickgrid/slick.remotemodel.js',
			'wpdapro_grid_editor_slick_remotemodel_yahoo'               => 'slickgrid/slick.remotemodel-yahoo.js',
			'wpdapro_grid_editor_controls_slick_columnpicker'           => 'slickgrid/controls/slick.columnpicker.js',
			'wpdapro_grid_editor_controls_slick_gridmenu'               => 'slickgrid/controls/slick.gridmenu.js',
			'wpdapro_grid_editor_controls_slick_pager'                  => 'slickgrid/controls/slick.pager.js',
			'wpdapro_grid_editor_plugins_slick_autotooltips'            => 'slickgrid/plugins/slick.autotooltips.js',
			'wpdapro_grid_editor_plugins_slick_cellcopymanager'         => 'slickgrid/plugins/slick.cellcopymanager.js',
			'wpdapro_grid_editor_plugins_slick_cellexternalcopymanager' => 'slickgrid/plugins/slick.cellexternalcopymanager.js',
			'wpdapro_grid_editor_plugins_slick_cellmenu'                => 'slickgrid/plugins/slick.cellmenu.js',
			'wpdapro_grid_editor_plugins_slick_cellrangedecorator'      => 'slickgrid/plugins/slick.cellrangedecorator.js',
			'wpdapro_grid_editor_plugins_slick_cellrangeselector'       => 'slickgrid/plugins/slick.cellrangeselector.js',
			'wpdapro_grid_editor_plugins_slick_cellselectionmodel'      => 'slickgrid/plugins/slick.cellselectionmodel.js',
			'wpdapro_grid_editor_plugins_slick_checkboxselectcolumn'    => 'slickgrid/plugins/slick.checkboxselectcolumn.js',
			'wpdapro_grid_editor_plugins_slick_contextmenu'             => 'slickgrid/plugins/slick.contextmenu.js',
			'wpdapro_grid_editor_plugins_slick_draggablegrouping'       => 'slickgrid/plugins/slick.draggablegrouping.js',
			'wpdapro_grid_editor_plugins_slick_headerbuttons'           => 'slickgrid/plugins/slick.headerbuttons.js',
			'wpdapro_grid_editor_plugins_slick_headermenu'              => 'slickgrid/plugins/slick.headermenu.js',
			'wpdapro_grid_editor_plugins_slick_resizer'                 => 'slickgrid/plugins/slick.resizer.js',
			'wpdapro_grid_editor_plugins_slick_rowdetailview'           => 'slickgrid/plugins/slick.rowdetailview.js',
			'wpdapro_grid_editor_plugins_slick_rowmovemanager'          => 'slickgrid/plugins/slick.rowmovemanager.js',
			'wpdapro_grid_editor_plugins_slick_rowselectionmodel'       => 'slickgrid/plugins/slick.rowselectionmodel.js',
		];

		public static function enqueue_styles() {
			// Prepare styles
			foreach ( self::slickgrid_styles as $style => $file ) {
				wp_register_style(
					$style,
					plugins_url( "../../../assets/premium/{$file}", __FILE__ ),
					[],
					WPDA::get_option( WPDA::OPTION_WPDA_VERSION )
				);
			}
		}

		public static function enqueue_scripts() {
			// Prepare script files
			foreach ( self::slickgrid_scripts as $script => $file ) {
				wp_register_script(
					$script,
					plugins_url( "../../../assets/premium/{$file}", __FILE__ ),
					[ 'jquery' ],
					WPDA::get_option( WPDA::OPTION_WPDA_VERSION )
				);
			}
		}

		public function shortcode_wpdagrid( $atts ) {
			wp_enqueue_style( 'wp-jquery-ui-core' );
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
			wp_enqueue_style( 'wp-jquery-ui-tabs' );
			wp_enqueue_style( 'wp-jquery-ui-sortable' );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-dialog' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-tooltip' );
			wp_enqueue_script( 'jquery-ui-droppable' );
			wp_enqueue_script( 'jquery-ui-draggable' );

			wp_enqueue_script( 'wpda_notify' );

			wp_enqueue_script( 'wpdapro_grid_editor_wpda_jquery_event_drag' );

			wp_enqueue_style( 'wpdapro_grid_editor_wpda_datagrid' );

			wp_enqueue_style( 'wpdapro_grid_editor_slick_grid' );
			wp_enqueue_style( 'wpdapro_grid_editor_css_slick_smoothness' );
			wp_enqueue_style( 'wpdapro_grid_editor_controls_slick_columnpicker' );
			wp_enqueue_style( 'wpdapro_grid_editor_controls_slick_pager' );

			wp_enqueue_script( 'wpdapro_grid_editor_slick_core' );
			wp_enqueue_script( 'wpdapro_grid_editor_plugins_slick_cellrangedecorator' );
			wp_enqueue_script( 'wpdapro_grid_editor_plugins_slick_cellrangeselector' );
			wp_enqueue_script( 'wpdapro_grid_editor_plugins_slick_cellselectionmodel' );
			wp_enqueue_script( 'wpdapro_grid_editor_slick_grid' );
			wp_enqueue_script( 'wpdapro_grid_editor_slick_formatters' );
			wp_enqueue_script( 'wpdapro_grid_editor_slick_editors' );
			wp_enqueue_script( 'wpdapro_grid_editor_plugins_slick_rowselectionmodel' );
			wp_enqueue_script( 'wpdapro_grid_editor_slick_dataview' );
			wp_enqueue_script( 'wpdapro_grid_editor_controls_slick_pager' );
			wp_enqueue_script( 'wpdapro_grid_editor_controls_slick_columnpicker' );

			wp_enqueue_script( 'wpdapro_grid_editor_wpda_ajax_calls' );
			wp_enqueue_script( 'wpdapro_grid_editor_wpda_datagrid' );
			wp_enqueue_script( 'wpdapro_grid_editor_wpda_editor' );

			ob_start();

			$schema_name = 'wordpress';
			$table_name  = 'dept';

			?>

			<div id="myGrid" class="wpda_datagrid"></div>
            
<!--            <div id="myGrid2" class="wpda_datagrid"></div>-->
			
            <div id="pager"></div>

			<style>
				.wpda_datagrid {
					width: 100%;
					height: 400px;
				}
			</style>

			<script type="text/javascript">
				var wpnonce_query  = '<?php echo wp_create_nonce( "wpdapro-datagrid-query-{$table_name}" ); ?>';
				var wpnonce_insert = '<?php echo wp_create_nonce( "wpdapro-datagrid-insert-{$table_name}" ); ?>';
				var wpnonce_update = '<?php echo wp_create_nonce( "wpdapro-datagrid-update-{$table_name}" ); ?>';
				var wpnonce_delete = '<?php echo wp_create_nonce( "wpdapro-datagrid-delete-{$table_name}" ); ?>';

				var wp_ajax_url  = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
				var databaseName = '<?php echo esc_attr( $schema_name ); ?>';
				var tableName    = '<?php echo esc_attr( $table_name ); ?>';

				jQuery(function() {
					createDataGrid(tableName).then();
				});
			</script>

			<?php

			return ob_get_clean();
		}

	}

}
