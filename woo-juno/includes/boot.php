<?php

namespace WC_Juno;

use WC_Juno\functions as h;

// init the loggers
$logger_handler = h\config_set( '$logger_handler', new Simple_Logger_Handler() );
$logger_handler->add_hooks();

// check plugin dependencies
$deps = h\config_set( '$deps', new Plugin_Dependencies() );
$deps->add_hooks();
