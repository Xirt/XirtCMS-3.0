<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Profiler Sections
| -------------------------------------------------------------------------
| This file lets you determine whether or not various sections of Profiler
| data are displayed when the Profiler is enabled.
| Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/profiling.html
|
*/

// CodeIgniter Config variables
$config['config'] = false;

// The Controller class and method requested
$config['controller_info'] = false;

// Any GET data passed in the request
$config['get'] = false;

// The HTTP headers for the current request
$config['http_headers'] = false;

// Any POST data passed in the request
$config['post'] = false;

// The URI of the current request
$config['uri_string'] = false;

// Data stored in the current session
$config['session_data'] = false;

// The number of queries after which the query block will default to hidden.
$config['query_toggle_count'] = 1;