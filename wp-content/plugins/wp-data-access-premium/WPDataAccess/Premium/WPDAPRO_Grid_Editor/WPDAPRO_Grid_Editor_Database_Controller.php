<?php

namespace WPDataAccess\Premium\WPDAPRO_Grid_Editor {

	class WPDAPRO_Grid_Editor_Database_Controller {
		protected $schema_name;
		protected $table_name;

		// Main constructor
		public function __construct( $table_name ) {
			$this->table_name = $table_name;
		}

		// Get headers (and corresponding datatypes) from database table
		public function getHeadersFromTable() {
			// Security check
			$wp_nonce = isset( $_REQUEST['wpnonce_query'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wpnonce_query'] ) ) : '?'; // input var okay.
			if ( ! wp_verify_nonce( $wp_nonce, "wpdapro-datagrid-query-{$this->table_name}" ) ) {
				return [ false, __( 'ERROR: Not authorized', 'wp-data-access' ) ];
			}

			global $wpdb;

			$jsonResult = [];
			$dataTypes  = [];

			// TODO Check table access
			// Use back ticks to prevent SQL injection
			$result = $wpdb->get_results( "show columns from `{$this->table_name}`", 'ARRAY_A' );
			foreach ( $result as $row ) {
				// Options can de added to this object
				$jsonResult[] = [
					'id'    => $row['Field'], // IMPORTANT Note: Id is used for table editing???
					'name'  => $row['Field'], // IMPORTANT Note: name is the displayname on page
					'field' => $row['Field'], // IMPORTANT Note: field is used for databinding
                    'sortable' => true        // Makes a column sortable or not
				];

				$dataTypes[] = $row['Type'];
			}

			return [ $jsonResult, $dataTypes ];
		}

		// Get all records from database table
		public function getRecordsFromTable() {
			// Security check
			$wp_nonce = isset( $_REQUEST['wpnonce_query'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wpnonce_query'] ) ) : '?'; // input var okay.
			if ( ! wp_verify_nonce( $wp_nonce, "wpdapro-datagrid-query-{$this->table_name}" ) ) {
				return [ false, __( 'ERROR: Not authorized', 'wp-data-access' ) ];
			}

			global $wpdb;

			// TODO Check table access
			// Use back ticks to prevent SQL injection
			return $wpdb->get_results( "select * from `{$this->table_name}`", 'ARRAY_A' );
		}

		public function addRecordToTable( $newRecord, $wpnonce ) {
			// Security check
			if ( ! wp_verify_nonce( $wpnonce, "wpdapro-datagrid-insert-{$this->table_name}" ) ) {
				return [ false, __( 'ERROR: Not authorized', 'wp-data-access' ) ];
			}

			global $wpdb;

			// TODO Check table access
			// Add sanitization for $newRecord->newItem
			$rows = $wpdb->insert(
				$newRecord->tableName,
				json_decode( json_encode( $newRecord->newItem ), true ) // convert to named array
			);

			if ( $rows === 1 ) {
				return [ true, "SUCCESS - $rows record(s) inserted" ];
			} else {
				return [ false, "ERROR - Insert failed" ];
			}
		}

		public function updateRecordInTable( $updateValues, $wpnonce ) {
			// Security check
			if ( ! wp_verify_nonce( $wpnonce, "wpdapro-datagrid-update-{$this->table_name}" ) ) {
				return [ false, __( 'ERROR: Not authorized', 'wp-data-access' ) ];
			}

			global $wpdb;

			$valueToChange = $updateValues->valueToChange;
			$itemToUpdate  = $updateValues->item;

			$updateStatement = $this->createUpdateStatement( $valueToChange );
			$whereClause     = $this->createUpdateDeleteWhereClause( $itemToUpdate );

			$sql  = $updateStatement . $whereClause;
			$rows = $wpdb->query( $sql );

			if ( $rows > 0 ) {
				return [ true, "SUCCESS - {$rows} records updated" ];
			} else {
				return [ false, "ERROR - No records updated" ];
			}
		}

		public function deleteRecord( $objectToDelete, $wpnonce ) {
			// Security check
			if ( ! wp_verify_nonce( $wpnonce, "wpdapro-datagrid-delete-{$this->table_name}" ) ) {
				return [ false, __( 'ERROR: Not authorized', 'wp-data-access' ) ];
			}

			global $wpdb;

			// TODO Check table access
			// Add sanitization for $objectToDelete
			$whereClause = $this->createUpdateDeleteWhereClause( $objectToDelete );
			$sql         = "DELETE FROM {$this->table_name} {$whereClause}";

			$rows = $wpdb->query( $sql );

			if ( $rows > 0 ) {
				return [ true, "SUCCESS - {$rows} records deleted" ];
			} else {
				return [ false, "ERROR - No records deleted" ];
			}
		}

		// UpdateValues
		private function createUpdateStatement( $valueToChange ) {
			$updateStatement = "UPDATE " . $this->table_name . " SET ";
			$i               = 0;
			foreach ( $valueToChange as $key => $value ) {
				if ( $key == "id" ) continue;
				if ( is_object( $value ) ) continue; // No objects please
				if ( is_numeric( $value ) and $i == 0 ) {
					$updateStatement = $updateStatement . $key . " = " . $value;
				} else if ( ! is_numeric( $value ) and $i == 0 ) {
					$updateStatement = $updateStatement . $key . " = '" . $value . "'";
				} else if ( is_numeric( $value ) and $i != 0 ) {
					$updateStatement = $updateStatement . ", " . $key . " = " . $value;
				} else if ( ! is_numeric( $value ) and $i != 0 ) {
					$updateStatement = $updateStatement . ", " . $key . " = '" . $value . "'";
				}
				$i++;
			}
			return $updateStatement;
		}

		// Where clause for update, delete query's
		private function createUpdateDeleteWhereClause( $updateItem ) {
			$whereClause = " WHERE ";
			$i           = 0;
			foreach ( $updateItem as $key => $value ) {
				if ( $key == "id" ) continue;
				if ( $key == "delBtnCol" ) continue;
				if ( $key == "crtButtonCol" ) continue;
				if ( $value == NULL ) continue;
				if ( is_object( $value ) ) continue; // No objects please
    
				$andClause = $key . " = '" . $value . "'";

				if ( $i === 0 ) {
					$whereClause = " {$whereClause} {$andClause} ";
				} else {
					$whereClause = " {$whereClause} AND {$andClause} ";
				}

				$i++;
			}

			return $whereClause;
		}
	}

}