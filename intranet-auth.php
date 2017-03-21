#!/usr/bin/php
<?php
/*
 * intranet-auth
 *
 * Copyright (c) 2017 Volksmission Freudenstadt, http://www.volksmission-freudenstadt.de
 * Author: Christoph Fischer, chris@toph.de
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
define( 'PASS', 0 );
define( 'ERROR_GENERAL', 1 );
define( 'ERROR_USER_NOT_FOUND', 2 );
define( 'ERROR_WRONG_PASSWORD', 3 );
define( 'ERROR_DB', 4 );
define( 'ERROR_ON_QUERY', 5 );

$exitCode = ERROR_GENERAL;

$config = yaml_parse_file(dirname(__FILE__).'/auth.yaml');

$handle   = fopen( "php://stdin", "r" );
$username = trim( fgets( $handle ) );
$password = trim( fgets( $handle ) );


$fp = fopen( '/tmp/auth.log', 'a' );
$db = new mysqli( $config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name'] );
if ( mysqli_connect_errno() ) {
	printf( "Verbindung fehlgeschlagen: %s\n", mysqli_connect_error() );
	exit( ERROR_DB );
}

$query = $db->prepare( 'SELECT password FROM ko_admin WHERE (login=?)' );
$query->bind_param( 's', $username );
$query->execute();

if ( $res = $query->get_result() ) {
	if ( $row = $res->fetch_assoc() )
    {
		if ( $row['password'] == md5( $password ) ) {
			$exitCode = PASS;
		} else {
			$exitCode = ERROR_WRONG_PASSWORD;
		}
	} else {
		$exitCode = ERROR_USER_NOT_FOUND;
	}
} else {
    $exitCode = ERROR_ON_QUERY;
}

fwrite( $fp,
	strftime( '%Y-%m-%d %H:%M:%S' ) . " Attempt to login as '{$username}' with password '{$password}' --> {$exitCode}" . PHP_EOL );
fclose( $fp );


exit ( $exitCode );