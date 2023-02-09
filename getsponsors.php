<?php
//ini_set('display_errors', 1);
if(isset($_GET['name'])){
	$response = array();
	$sponsorer = $_GET['name'];
	$sponsorsUrl = "https://github.com/sponsors/" . $sponsorer;
	$html = file_get_contents($sponsorsUrl);
	libxml_use_internal_errors( true);
	$doc = new DOMDocument;
	$doc->loadHTML( $html);
	$xpath = new DOMXpath( $doc);
	$node = $xpath->query( '//h4[@class="mb-3"]')->item( 0);
	//echo $node->textContent; // This will print **GET THIS TEXT**
	$totalSponsors = $node->textContent;
	$res = preg_replace("/[^0-9]/", "", $totalSponsors );
	$totalSponsorCount = intval($res);
	$totalPages = round($totalSponsorCount / 50);
	
	$testarray = array();

	for($page = 1; $page <= $totalPages; $page++){
		$url = "https://github.com//sponsors/".$sponsorer."/sponsors_partial?filter=active&page=".$page;
		$html = file_get_contents($url);
		$doc = new DOMDocument();
		$doc->loadHTML($html); //helps if html is well formed and has proper use of html entities!
		$xpath = new DOMXpath($doc);
		$nodes = $xpath->query('//a[@class="d-inline-block"]');
		foreach($nodes as $node) {
			$workingString = $node->getAttribute('href');
			$workedString = str_replace("/", "", $workingString);
			array_push($testarray, $workedString);
		}

	}

	$response["data"] = $testarray;
	http_response_code(200);
        header('Content-type: application/json');
	echo json_encode($response);
} else {
	echo "Please set the name of the user that you want to get sponsor data names of.";
}
?>
