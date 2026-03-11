<?php

if( isset( $_REQUEST[ 'Submit' ] ) ) {
	// Get input
	$id = $_REQUEST[ 'id' ];

	switch ($_DVWA['SQLI_DB']) {
		case MYSQL:
			// FIXED: parameterized query prevents SQL injection
			$stmt = mysqli_prepare(
				$GLOBALS["___mysqli_ston"],
				"SELECT first_name, last_name FROM users WHERE user_id = ?"
			);
			if ( !$stmt ) {
				die( '<pre>Prepare failed: ' . mysqli_error( $GLOBALS["___mysqli_ston"] ) . '</pre>' );
			}
			mysqli_bind_param( $stmt, 's', $id );
			mysqli_stmt_execute( $stmt );
			$result = mysqli_stmt_get_result( $stmt );

			// Get results
			while( $row = mysqli_fetch_assoc( $result ) ) {
				$first = $row["first_name"];
				$last  = $row["last_name"];
				$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
			}

			mysqli_stmt_close( $stmt );
			mysqli_close( $GLOBALS["___mysqli_ston"] );
			break;

		case SQLITE:
			global $sqlite_db_connection;

			// FIXED: parameterized query prevents SQL injection
			$stmt = $sqlite_db_connection->prepare(
				"SELECT first_name, last_name FROM users WHERE user_id = ?"
			);
			$stmt->bindValue( 1, $id, SQLITE3_TEXT );
			$results = $stmt->execute();

			if ( $results ) {
				while ( $row = $results->fetchArray() ) {
					$first = $row["first_name"];
					$last  = $row["last_name"];
					$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
				}
			} else {
				echo "Error in fetch";
			}
			break;
	}
}

?>
