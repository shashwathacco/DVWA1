<?php

if( isset( $_GET[ 'Submit' ] ) ) {
	// Get input
	$id     = $_GET[ 'id' ];
	$exists = false;

	switch ($_DVWA['SQLI_DB']) {
		case MYSQL:
			// FIXED: parameterized query prevents SQL injection
			$stmt = mysqli_prepare(
				$GLOBALS["___mysqli_ston"],
				"SELECT first_name, last_name FROM users WHERE user_id = ?"
			);
			if ( $stmt ) {
				mysqli_bind_param( $stmt, 's', $id );
				mysqli_stmt_execute( $stmt );
				$result = mysqli_stmt_get_result( $stmt );
				if ( $result !== false ) {
					try {
						$exists = ( mysqli_num_rows( $result ) > 0 );
					} catch ( Exception $e ) {
						$exists = false;
					}
				}
				mysqli_stmt_close( $stmt );
			}
			((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
			break;

		case SQLITE:
			global $sqlite_db_connection;

			// FIXED: parameterized query prevents SQL injection
			$stmt = $sqlite_db_connection->prepare(
				"SELECT first_name, last_name FROM users WHERE user_id = ?"
			);
			$stmt->bindValue( 1, $id, SQLITE3_TEXT );
			$results  = $stmt->execute();
			$row      = $results ? $results->fetchArray() : false;
			$exists   = ( $row !== false );
			break;
	}

	if ( $exists ) {
		$html .= '<pre>User ID exists in the database.</pre>';
	} else {
		header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' 404 Not Found' );
		$html .= '<pre>User ID is MISSING from the database.</pre>';
	}
}

?>
