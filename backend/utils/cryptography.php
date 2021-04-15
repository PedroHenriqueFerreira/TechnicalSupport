<?php
function cryptography( $string, $action = 'encrypt' ) {
    // you may change these values to your own
    $secret_key = 'Fdemv2OW2FE8MWo6fw4fo63';
    $secret_iv = 'vrniEi4tVNDi2302CeifefoOWe';

    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

    if( $action == 'encrypt' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'decrypt' ){
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }

    return $output;
}