function AllAjaxCalls() {
    ////// GET endpoint calls
    // Retrieves headers of the table
    this.getHeaders = async function (tableName) {
        return jQuery.ajax({
            type: 'GET',
            url: wp_ajax_url + '?action=wpdapro_datagrid_get_headers&tableName=' + tableName + '&wpnonce_query=' + wpnonce_query,
            success: function(response) {
                if (!response[0]) {
                    jQuery.notify('Internal server error', 'error');
                }
                return response;
            },
            error: function (response) {
                jQuery.notify('Internal server error', 'error');
                return response;
            },
            complete: function (response) {
                // console.log("Server response getHeaders", response);
            }
        });
    }
    
    // Retrieves data to populate the table
    this.getContent = async function (tableName) {
        return jQuery.ajax({
            type: 'GET',
            url: wp_ajax_url + '?action=wpdapro_datagrid_get_records&tableName=' + tableName + '&wpnonce_query=' + wpnonce_query,
            success: function(response) {
                if (!response[0]) {
                    jQuery.notify('Internal server error', 'error');
                }
                return response;
            },
            error: function (response) {
                jQuery.notify('Internal server error', 'error');
                return response;
            },
            complete: function (response) {
                // console.log("Server response getContent", response);
            }
        });
    }
    
    ////// POST endpoint calls
    this.createNewRecord = function (tableName, item, callback) {
        jQuery.ajax({
            type: 'POST',
            url: wp_ajax_url + '?action=wpdapro_datagrid_create_record',
            dataType: 'json',
            data: JSON.stringify({
                newItem: item,
                tableName: tableName,
                wpnonce_insert: wpnonce_insert
            }),
            success: function(response) {
                callback();
                jQuery.notify(response[1], response[0] ? 'success' : 'error');
                return response;
            },
            error: function (response) {
                jQuery.notify('Internal server error', 'error');
                return response;
            },
            complete: function (response) {
                // console.log("Server response createNewRecord", response);
            }
        });
    }
    
    ////// PUT endpoint calls
    this.updateExistingRecord = function (tableName, item, valueToChange, callback) {
        jQuery.ajax({
            type: 'PUT',
            url: wp_ajax_url + '?action=wpdapro_datagrid_update_record',
            dataType: 'json',
            data: JSON.stringify({
                item: item, // Contains the dbRecord
                valueToChange: valueToChange, // Contains key and value to update
                tableName: tableName, // Contains the name of the dbTable
                wpnonce_update: wpnonce_update
            }),
            success: function(response) {
                callback();
                jQuery.notify(response[1], response[0] ? 'success' : 'error');
                return response;
            },
            error: function (response) {
                jQuery.notify('Internal server error', 'error');
                return response;
            },
            complete: function (response) {
                // console.log("Server response updateExistingRecord", response);
            }
        });
    }
    
    ////// DELETE endpoint calls
    this.removeSelectedRecord = function (tableName, itemToRemove, callback, rowNumber) {
        jQuery.ajax({
            type: 'DELETE',
            url: wp_ajax_url + '?action=wpdapro_datagrid_delete_record',
            dataType: 'json',
            data: JSON.stringify({
                tableName: tableName,
                itemToRemove: itemToRemove,
                wpnonce_delete: wpnonce_delete
            }),
            success: function(response) {
                callback(rowNumber);
                jQuery.notify(response[1], response[0] ? 'success' : 'error');
                return response;
            },
            error: function (response) {
                jQuery.notify('Internal server error', 'error');
                return response;
            },
            complete: function (response) {
                console.log("Server response removeSelectedRecord", response);
            }
        });
    }
}