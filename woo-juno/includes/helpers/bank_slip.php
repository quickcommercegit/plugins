<?php

namespace WC_Juno\functions;

use Picqer\Barcode\BarcodeGeneratorSVG as BarcodeGenerator;

function generate_barcode ( $code, $widthFactor = 2, $totalHeight = 30, $color = 'black' ) {
	$generator = new BarcodeGenerator();
	return $generator->getBarcode( $code, $generator::TYPE_INTERLEAVED_2_5, $widthFactor, $totalHeight, $color );
}
