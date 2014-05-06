<?php
	          $halls =array(
			  "Alexander Dining Hall",
			   "Anderson Hall",
				  "Bishop Hall",
				  "Brandon Hall",
				  "Clawson Hall",
				 "Collins Hall",
				  "Dennison Hall",
				 "Dodds Hall",
				 "Dorsey Hall",
				  "Elliott Hall",
				  "Emerson Hall",
				 "Erickson Dining Hall",
				 "Flower Hall", 
				  "Hahne Hall",
				 "Hamilton Hall",
				  "Harris Dining Hall",
				  "Havighurst Hall",
				 "Hepburn Hall",
				 "MacCracken Hall",
				"Martin Dining Hall",
				  "Mary Lyon Hall",
				  "McBride Hall",
				 "McFarland Hall",
				 "McKee Hall",
				 "Miami Inn",
				 "Minnich Hall", 
				 "Morris Hall",
				  "Ogden Hall",
				 "Peabody Hall",
				  "Porter Hall",
				  "Richard Hall",
				 "Scott Hall",
				  "Stanton Hall",
				 "Stoddard Hall",
				 "Swing Hall",
				 "Symmes Hall",
			 "Tappan Hall",	
				  "Thomson Hall",
				"Wells Hall", 
				 "Wilson Hall" );
				 
				 $account_types=array(
				 array('value' => '1', 'text' => 'Administrator'),
				 array('value' => '3', 'text' => 'Presenter'),
				 array('value' => '2', 'text' => 'Customer'),
				 );
				
$uri = $_SERVER['REQUEST_URI'];
$method = 'GET';

$paths = explode('/', $uri);

if ($paths[6] == 'locations') {
        process_list($method);
} else if($paths[6] == 'account-types') {
        get_types();
} else {
    header('HTTP/1.1 404 Not Found');
}





function process_list($method) {
    global $halls;
    switch($method) {
    case 'GET':
        result();
        break;
    default:
        header('HTTP/1.1 405 Method Not Allowed');
        header('Allow: GET');
        break;
    }
}

function result() {
    global $halls;
    header('Content-type: application/json');
    echo json_encode($halls);
}

function get_types() {
    global $account_types;
    header('Content-type: application/json');
    echo json_encode($account_types);
}

?>
