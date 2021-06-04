<?php

namespace WC_Juno\functions;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

function generate_qrcode( $payload ) {
  $options = new QROptions([
    'outputLevel' => 0b00,
    'outputType' => QRCode::OUTPUT_MARKUP_SVG,
  ]);

  return ( new QRCode( $options ) )->render( $payload );
}
