<?php

/**
 * Suppress "error - 0 - No summary was found for this file" on phpdoc generation
 *
 * @package WPDataAccess\Plugin_Table_Models
 */

namespace WPDataAccess\Premium\WPDAPRO_Grid_Editor {

	/**
	 * Class WPDA_Plugin_Table_Base_Model
	 *
	 * Model for plugin table 'wpda_datagrid'
	 *
	 * @author  Max Schulz
	 * @since   3.5.0
	 */
	class WPDAPRO_Grid_Editor_Actions {

		public function get_headers() {
			$this->response_header();

			$tableName = isset( $_REQUEST['tableName'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tableName'] ) ) : ''; // input var okay.

			if ( ! is_null( $tableName ) ) {
				$facade = WPDAPRO_Grid_Editor_Action_Handler::constructGet( 'getHeaders', $tableName );
				$facade->processRequest();
			} else {
				echo new stdClass();
			}

			wp_die();
		}

		public function get_records() {
			$this->response_header();

			$tableName = isset( $_REQUEST['tableName'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tableName'] ) ) : null; // input var okay.

			if ( ! is_null( $tableName ) ) {
				$facade = WPDAPRO_Grid_Editor_Action_Handler::constructGet( 'getDbRecords', $tableName );
				$facade->processRequest();
			} else {
				echo new stdClass();
			}

			wp_die();
		}

		public function create_record() {
			$this->response_header();

			$request_body_string = file_get_contents("php://input");
			$request_body_object = json_decode( $request_body_string );

			$facade = WPDAPRO_Grid_Editor_Action_Handler::constructPost( 'createNewRecord', $request_body_object );
			$facade->processRequest();

			wp_die();
		}

		public function update_record() {
			$this->response_header();

			$request_body_string = file_get_contents("php://input");
			$request_body_object = json_decode( $request_body_string );

			$facade = WPDAPRO_Grid_Editor_Action_Handler::constructPut( 'updateRecord', $request_body_object );
			$facade->processRequest();

			wp_die();
		}

		public function delete_record() {
			$this->response_header();

			$request_body_string = file_get_contents("php://input");
			$request_body_object = json_decode( $request_body_string );

			$facade = WPDAPRO_Grid_Editor_Action_Handler::constructDelete( 'deleteRecord', $request_body_object );
			$facade->processRequest();

			wp_die();
		}

		private function response_header() {
			header( "Access-Control-Allow-Origin: *" );
			header( "Access-Control-Allow-Methods: GET, PUT, POST, DELETE" );
			header( "Access-Control-Max-Age: 0" );
			header( "Cache-Control: no-cache" );
			header( 'Content-Type: application/json; charset=utf-8' );
		}

	}

}