<?php

namespace WPDataAccess\Premium\WPDAPRO_Grid_Editor {

	class WPDAPRO_Grid_Editor_Action_Handler {

		private $requestMethod;
		private $actionToExecute;

		private $tableName;
		private $requestBody;

		private $databaseController;
		private $httpResponse;

		// An object that can be filled with a different object each time
		private $objectToUse;

		// Can be used for cases no concerning another method
		private $options;

		private $wpnonce = null;

		public function __construct() {
			$this->httpResponse;
		}

		// GET constructor
		public static function constructGet( $actionToExecute, $tableToFind ) {
			$instance = new self();

			$instance->requestMethod   = "GET";
			$instance->actionToExecute = $actionToExecute;

			$instance->tableName = $tableToFind;
			$instance->databaseController = new WPDAPRO_Grid_Editor_Database_Controller($instance->tableName);

			return $instance;
		}

		// PUT constructor
		public static function constructPut( $actionToExecute, $requestBody ) {
			$instance = new self();

			$instance->requestMethod   = "PUT";
			$instance->actionToExecute = $actionToExecute;
			$instance->requestBody     = $requestBody;

			// A PUT request has the tableName in the body rather than the URI
			$instance->tableName          = sanitize_text_field( wp_unslash( $requestBody->tableName ) ); // input var okay.
			$instance->wpnonce            = sanitize_text_field( wp_unslash( $requestBody->wpnonce_update ) ); // input var okay.
			$instance->databaseController = new WPDAPRO_Grid_Editor_Database_Controller( $instance->tableName );

			return $instance;
		}

		// POST constructor
		public static function constructPost( $actionToExecute, $requestBody ) {
			$instance = new self();

			$instance->requestMethod   = "POST";
			$instance->actionToExecute = $actionToExecute;
			$instance->requestBody     = $requestBody;

			// A POST request has the tableName in the body rather than the URI
			$instance->tableName          = sanitize_text_field( wp_unslash( $requestBody->tableName ) ); // input var okay.
			$instance->wpnonce            = sanitize_text_field( wp_unslash( $requestBody->wpnonce_insert ) ); // input var okay.
			$instance->databaseController = new WPDAPRO_Grid_Editor_Database_Controller( $instance->tableName );

			return $instance;
		}

		// DELETE constructor
		public static function constructDelete( $actionToExecute, $requestBody ) {
			$instance = new self();

			$instance->requestMethod   = "DELETE";
			$instance->actionToExecute = $actionToExecute;

			// A DELETE request has the tableName in the body rather than the URI
			$instance->tableName          = sanitize_text_field( wp_unslash( $requestBody->tableName ) ); // input var okay.
			$instance->wpnonce            = sanitize_text_field( wp_unslash( $requestBody->wpnonce_delete ) ); // input var okay.
			$instance->objectToUse        = $requestBody->itemToRemove;
			$instance->databaseController = new WPDAPRO_Grid_Editor_Database_Controller( $instance->tableName );

			return $instance;
		}

		// OPTIONS constructor
		public static function constructOptions( $actionToExecute, $tableToFind, $options ) {
			$instance = new self();

			$instance->requestMethod   = "OPTIONS";
			$instance->actionToExecute = $actionToExecute;

			$instance->tableName          = $tableToFind;
			$instance->databaseController = new WPDAPRO_Grid_Editor_Database_Controller( $instance->tableName );

			$instance->options = $options;

			return $instance;
		}

		public function processRequest() {
			switch ( $this->requestMethod ) {
				case 'GET':
					if ( $this->actionToExecute == "getHeaders" ) {
						// Get headers from table
						$this->httpResponse = $this->readHeadersContent();
					} else if ( $this->actionToExecute == "getDbRecords" ) {
						// Get content of table
						$this->httpResponse = $this->getDatabaseRecords();
					} else if ( $this->actionToExecute == "getSingleRecord" ) {
						$this->httpResponse = $this->getSingleRecord();
					} else {
						// Handle exception
						$this->httpResponse = $this->notFoundResponse();
					}
					break;
				case 'POST':
					if ( $this->actionToExecute == "createNewRecord" ) {
						$this->httpResponse = $this->createRecord();
					} else {
						// Handle exception
						$this->httpResponse = $this->notFoundResponse();
					}
					break;
				case 'PUT':
					if ( $this->actionToExecute == "updateRecord" ) {
						$this->httpResponse = $this->updateRecord();
					} else {
						// Handle exception
						$this->httpResponse = $this->notFoundResponse();
					}
					break;
				case 'DELETE':
					$this->httpResponse = $this->deleteRecord();
					break;
				case 'OPTIONS':
					if ( $this->actionToExecute == "readColumnTypes" ) {
						//$this->httpResponse = $this->readColumnTypes();
					}
					break;
				default:
					$this->httpResponse = $this->notFoundResponse();
					break;
			}

			header( $this->httpResponse["status_code_header"] );
			if ( $this->httpResponse['body'] ) {
				echo $this->httpResponse['body'];
			}
		}

		private function readHeadersContent() // Read headers DONE
		{
			$jsonResult = $this->databaseController->getHeadersFromTable();

			$response['status_code_header'] = 'HTTP/1.1 200 OK';
			$response['body']               = json_encode( $jsonResult );
			return $response;
		}

		private function getDatabaseRecords() {
			$result = $this->databaseController->getRecordsFromTable();

			$response['status_code_header'] = 'HTTP/1.1 200 OK';
			$response['body']               = json_encode( $result );
			return $response;
		}

		private function createRecord() {
			$result = $this->databaseController->addRecordToTable( $this->requestBody, $this->wpnonce );

			if ( $result[0] ) // 0 contains bool succeeded or not
			{
				$response['status_code_header'] = 'HTTP/1.1 201 CREATED';
				$response['body']               = json_encode( $result[1] );
				return $response;
			}
			$response['status_code_header'] = 'HTTP/1.1 404 NOT FOUND';
			$response['body']               = json_encode( $result[1] );
			return $response;
		}

		private function updateRecord() {
			$result = $this->databaseController->updateRecordInTable( $this->requestBody, $this->wpnonce );

			$response['status_code_header'] = 'HTTP/1.1 204 Resource updated successfully';
			$response['body']               = json_encode( $result );
			return $response;
		}

		private function deleteRecord() {
			$result = $this->databaseController->deleteRecord( $this->objectToUse, $this->wpnonce );

			$response['status_code_header'] = 'HTTP/1.1 204 No content';
			$response['body']               = json_encode( $result );
			return $response;
		}

		private function notFoundResponse() {
			$response['status_code_header'] = 'HTTP/1.1 404 Not Found response';
			$response['body']               = null;
			return $response;
		}
	}

}