function export_publication_selection_to_sql(schema_name, table_name, pub_id, wp_nonce, primary_key) {
	// Get table data
	table = jQuery('#' + table_name + pub_id ).DataTable();
	rows = table.rows({selected:true});
	if (table.rows({selected:true}).count()===0) {
		rows = table.rows();
	}
	data = rows.data();

	// Add key values to array
	ids = [];
	rows.every(function ( rowIdx, tableLoop, rowLoop ) {
		var row = jQuery(this.node());
		col = row.find('td').eq(1);
		ids.push(col.text());
	});

	// Define target url
	url =
		window.location.protocol + "//" +
		window.location.hostname +
		(window.location.port ? ":" + window.location.port : "" ) +
		"/wp-admin/admin-ajax.php";

	// Add default arguments to url
	url +=
		"?action=wpda_export&type=row&mysql_set=off&show_create=off&show_comments=off&format_type=sql" +
		"&schema_name=" + schema_name +
		"&table_names=" + table_name +
		"&_wpnonce=" + wp_nonce;

	// Add keys to url
	for (i=0; i<ids.length; i++) {
		for (var pk in primary_key) {
			url += '&' + primary_key[pk] + '[' + i + ']=' + encodeURIComponent(ids[i]);
		}
	}

	window.location.href = url;
}