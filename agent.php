<?php
$CLASS = 'agent'; class agent { 

private $tabStates = array();

private function clipboardput($v = '', $file = 'temp.txt') { 
    if (!$v) return; 
    $out = fopen($file, 'w'); 
    fwrite($out, "$v"); 
    fclose($out); 
    system("cat temp.txt > /dev/clipboard"); 
}


private function parseHotelEntries($text) {
    $entries = array();
    $lines = explode("\n", $text);
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Skip empty lines and comma-separated lists (likely price tags)
        if (empty($line) || strpos($line, ', ') !== false) {
            continue;
        }
        
        // Keep hotel entries which typically contain a name and price: "Hotel Name (price)"
        //if (strpos($line, '(') !== false && strpos($line, ')') !== false) {
        //    $entries[trim($line)] = true;
        //}

		$entries[trim($line)] = true;
    }
    
    return $entries;
}
private function markNewEntries($newEntries, $tabNo) {
    $markedText = "";
    $hasNewInfo = false;
    
    // Compare and mark new entries
    foreach ($newEntries as $entry => $value) {
        if (!isset($this->tabStates[$tabNo][$entry])) {
            // This is a new entry
            $markedText .= "★ " . $entry . "\n";
            $hasNewInfo = true;
        } else {
            // Existing entry
            $markedText .= $entry . "\n";
        }
    }
    
    // Update state with current entries
    $this->tabStates[$tabNo] = $newEntries;
    
    return array(
        'text' => $markedText,
        'hasNewInfo' => $hasNewInfo
    );
}


public function run() {
    `rm -Rf temp*`;
    $WINDOWNAME1 = 'Chrome';
    $WINDOWNAME2 = 'Saved';
    $TOPRICE = '80k';
    
    // Configuration for tabs - easily expandable
    $tabs = array(
        
		// Airbnb
		// https://www.airbnb.jp/s/%E4%BC%8A%E4%BF%9D%E7%94%B0%E6%B8%AF/homes?refinement_paths%5B%5D=%2Fhomes&checkin=2025-05-03&checkout=2025-05-04&adults=2&children=2&query=%E4%BC%8A%E4%BF%9D%E7%94%B0%E6%B8%AF&place_id=ChIJ9RtiMVpaRTURDI6y_BjpobI&flexible_trip_lengths%5B%5D=one_week&monthly_start_date=2025-05-01&monthly_length=3&monthly_end_date=2025-08-01&search_mode=regular_search&price_filter_input_type=2&price_filter_num_nights=1&channel=EXPLORE&ne_lat=34.00829191204745&ne_lng=132.3654647283505&sw_lat=33.75299625123473&sw_lng=132.0891987386296&zoom=11.883985619483822&zoom_level=11.883985619483822&search_by_map=true&search_type=user_map_move
		//array(
        //    'label' => 'IBOTA(5/3)',
        //    'prompt' => "The image is of airbnb looking at an area that I am interested in. The map on the right side of the screen shows currently available locations as price tags (in Japanese yen, as number).  All the displayed tags should also be shown as cards (images with text) on the left side of the screen.  Since the same price tag is used both on the map and in the list of cards, you can easily match which tags are connected to which cards.  If there are locations with prices below $TOPRICE , return them in 'Location name (price)' format. Put each location in a separate line so that I can parse the output.  Do not return locations with prices above $TOPRICE.  If no locations pass this filter, return only the single word 'nothing'. "
		//)
		
		// Google Maps
		// https://www.google.com/maps/search/hotels/@34.7110131,135.1110473,12.26z/data=!4m9!2m8!5m6!5m3!1s2025-05-03!4m1!1i2!9i50852!16i14773!6e3?authuser=0&entry=ttu&g_ep=EgoyMDI1MDQyMy4wIKXMDSoASAFQAw%3D%3D
		//array(
        //    'label' => 'GMAPS・Arima+(5/3)',
        //    'prompt' => "The image shows Google Maps with hotel search results. 1. FIRST, focus only on the left panel showing the list of hotels. List ONLY the hotel names you can see.  2. SECOND, look at the map section and note any visible price tags (often showing '¥XX万'). Price tags are notiable by a small red icon of a hotel and and tag itself is a round white box with black border.  Do not try to get all the price tags, just sample and parse up to 10 of them.   3. FINALLY, combine this information to show: Hotel Name (price) format where possible.  Return only what you can confidently read, maximum 10 items.  Try to pack the text compactly so that it would be readible in chat environments and would not be too long.  Yet, as I need to parse this output, return each hotel on a separate line.  Price labels only can all share one line in a comma-delimited list.  If you cannot read anything clearly, say 'nothing because of limited visibility'."
		//)

		// https://www.airbnb.jp/s/%E5%88%A5%E5%BA%9C/homes?refinement_paths%5B%5D=%2Fhomes&date_picker_type=calendar&checkin=2025-06-13&checkout=2025-06-14&source=structured_search_input_header&search_type=user_map_move&query=%E5%88%A5%E5%BA%9C&place_id=ChIJOXGzEpWTQTUR_0vMZ_blMqE&parent_city_place_id=ChIJKYSE6aHtQTURg4c5NplyCvY&flexible_trip_lengths%5B%5D=one_week&monthly_start_date=2025-07-01&monthly_length=3&monthly_end_date=2025-10-01&search_mode=regular_search&price_filter_input_type=2&price_filter_num_nights=1&channel=EXPLORE&ne_lat=33.32323793535567&ne_lng=131.47483374311997&sw_lat=33.297830915616714&sw_lng=131.4437144438802&zoom=15.673657261002397&zoom_level=15.673657261002397&search_by_map=true
		array(
            'label' => 'ABnB・BeppuDoroDoro',
            'prompt' => "The image is of airbnb looking at an area that I am interested in. The map on the right side of the screen shows currently available locations as price tags (in Japanese yen, as number).  All the displayed tags should also be shown as cards (images with text) on the left side of the screen.  Since the same price tag is used both on the map and in the list of cards, you can easily match which tags are connected to which cards.  If there are locations with prices below $TOPRICE , return them in 'Location name (price)' format. Put each location in a separate line so that I can parse the output.  Do not return locations with prices above $TOPRICE.  If no locations pass this filter, return only the single word 'nothing'. "
		),
		// https://www.google.com/maps/search/%E5%88%A5%E5%BA%9C+hotels/@33.3135819,131.458879,16.8z?entry=ttu&g_ep=EgoyMDI1MDYwMS4wIKXMDSoASAFQAw%3D%3D
		array(
            'label' => 'GMAPS・BeppuDoroDoro',
            'prompt' => "The image shows Google Maps with hotel search results. 1. FIRST, focus only on the left panel showing the list of hotels. List ONLY the hotel names you can see.  2. SECOND, look at the map section and note any visible price tags (often showing '¥XX万'). Price tags are notiable by a small red icon of a hotel and and tag itself is a round white box with black border.  Do not try to get all the price tags, just sample and parse up to 10 of them.   3. FINALLY, combine this information to show: Hotel Name (price) format where possible.  Return only what you can confidently read, maximum 10 items.  Try to pack the text compactly so that it would be readible in chat environments and would not be too long.  Yet, as I need to parse this output, return each hotel on a separate line.  Price labels only can all share one line in a comma-delimited list.  If you cannot read anything clearly, say 'nothing because of limited visibility'."
		)

	);
    
    while (true) { foreach ($tabs as $tabNo => $tabConfig) {
		`rm -Rf temp*`;

		// (1) Activate Chrome and switch to specific tab, then refresh
		$vs = file('STAB.chrome-refresh.ahk');
		foreach ($vs as $k => $v) {
			$v = str_replace('WINDOWNAME', $WINDOWNAME1, $v);
			$v = str_replace('TABNO', '' . ( $tabNo + 1), $v);
			$vs[$k] = $v;
		}
		$out = fopen('temp1.ahk', 'w'); 
		fwrite($out, implode('', $vs)); 
		fclose($out);
		`chmod -R 777 temp1.ahk`;
		$c = 'AutoHotkey temp1.ahk';
		procpipe($c, true, true);

		// (2) Take a screenshot using nircmd
		sleep(3); $label = $tabConfig['label'];
		`rm -Rf temp.jpg`;
		// full screen capture: maslovsky(3840x2160), lenovo(1366x768)
		$c = "nircmd savescreenshot temp.jpg 0 0 1366 768";
		procpipe($c, true, true); 
		`chmod -R 777 temp.jpg`;

		// (3) Create prompt.json for OpenAI image analysis
		$json = json_encode([
			'image_file' => 'temp.jpg',
			'prompt' => $tabConfig['prompt']
		]);
		file_put_contents('prompt.json', $json);

		// (4) Run Node.js script to analyze image
		$result = shell_exec('node analyze_image.js');
		$result = trim($result);

		// Skip processing if result is "nothing"
		if (strpos(strtolower($result), 'nothing') !== false) {
			continue;
		}

		if (!isset($this->tabStates[$tabNo])) {
			$this->tabStates[$tabNo] = array();
		}

		// Parse entries and detect new information
		echo "\n\n";
		echo "====== $label ======\n";
		echo $result . "\n";
		$entries = $this->parseHotelEntries($result);
		echo "---- old state [" . count($this->tabStates[$tabNo]) . "] ----\n";
		foreach ($this->tabStates[$tabNo] as $entry => $value) echo "[$entry]\n";
		echo "---- entries ----\n";
		foreach ($entries as $entry => $value) echo "$entry\n";
		$processed = $this->markNewEntries($entries, $tabNo);
		echo "---- processed ----\n";
		echo $processed['text'] . "\n";
		echo '--- has new info ? ' . ($processed['hasNewInfo'] ? 'yes' : 'no') . "\n";
		echo '--- new state [' . count($this->tabStates[$tabNo]) . "] ----\n";
		foreach ($this->tabStates[$tabNo] as $entry => $value) echo "[$entry]\n";
		echo "====================\n";

		// Only send message if there are entries
		if ($processed['hasNewInfo'] && !empty($processed['text'])) {
			// Prepend the label to the result for visual discrimination
			$messageText = "[{$tabConfig['label']}]\n" . $processed['text'];
			
			// Copy image to clipboard
			$c = 'nircmd clipboard copyimage temp.jpg';
			procpipe($c, true, true);
			
			// Activate Telegram and paste the image
			$vs = file('STAB.telegram-paste.ahk');
			foreach ($vs as $k => $v) $vs[$k] = str_replace('WINDOWNAME', $WINDOWNAME2, $v);
			$out = fopen('temp2.ahk', 'w'); 
			fwrite($out, implode('', $vs)); 
			fclose($out);
			`chmod -R 777 temp2.ahk`;
			$c = 'AutoHotkey temp2.ahk';
			procpipe($c, true, true);
			
			// Copy text to clipboard and paste to Telegram
			$this->clipboardput($messageText);
			procpipe($c, true, true); // Run the same AHK script again for text
			
			// Send the message with Alt+Enter
			$vs = file('STAB.telegram-send.ahk');
			foreach ($vs as $k => $v) $vs[$k] = str_replace('WINDOWNAME', $WINDOWNAME2, $v);
			$out = fopen('temp3.ahk', 'w'); 
			fwrite($out, implode('', $vs)); 
			fclose($out);
			`chmod -R 777 temp3.ahk`;
			$c = 'AutoHotkey temp3.ahk';
			procpipe($c, true, true);
		}
		
		// Wait a moment before checking the next tab
		sleep(5);
    }; sleep(50 * 60);}
}

}
if ( isset( $argv) && count( $argv) && strpos( $argv[ 0], "$CLASS.php") !== false) { // direct CLI execution, redirect to one of the functions 
	// this is a standalone script, put the header
	set_time_limit( 0);
	ob_implicit_flush( 1);
	//ini_set( 'memory_limit', '4000M');
	for ( $prefix = is_dir( 'ajaxkit') ? 'ajaxkit/' : ''; ! is_dir( $prefix) && count( explode( '/', $prefix)) < 4; $prefix .= '../'); if ( ! is_file( $prefix . "env.php")) $prefix = '/web/ajaxkit/'; 
	if ( ! is_file( $prefix . "env.php") && ! is_file( 'requireme.php')) die( "\nERROR! Cannot find env.php in [$prefix] or requireme.php in [.], check your environment! (maybe you need to go to ajaxkit first?)\n\n");
	if ( is_file( 'requireme.php')) require_once( 'requireme.php'); else foreach ( explode( ',', ".,$prefix") as $p) foreach ( array( 'functions', 'env') as $k) if ( is_dir( $p) && is_file( "$p/$k.php")) require_once( "$p/$k.php");
	$CLDIR = clgetdir(); //chdir( clgetdir());
	clparse(); $JSONENCODER = 'jsonraw'; // jsonraw | jsonencode    -- jump to lib dir
	// help
	clhelp( "FORMAT: php$CLASS WDIR COMMAND param1 param2 param3...     ($CLNAME)");
	foreach ( file( "$CLDIR/$CLNAME") as $line) if ( ( strpos( trim( $line), '// SECTION:') === 0 || strpos( trim( $line), 'public function') === 0) && strpos( $line, '__construct') === false) clhelp( lshift( ttl( trim( str_replace( 'public function', '', $line)), '{'))); // }
	// parse command line
	lshift( $argv); if ( ! count( $argv)) die( clshowhelp()); 
	//$wdir = lshift( $argv); if ( ! is_dir( $wdir)) { echo "ERROR! wdir#$wdir is not a directory\n\n"; clshowhelp(); die( ''); }
	//echo "wdir#$wdir\n"; if ( ! count( $argv)) { echo "ERROR! no action after wdir!\n\n"; clshowhelp(); die( ''); }
	$f = lshift( $argv); $C = new $CLASS(); chdir( $CWD); 
	switch ( count( $argv)) { case 0: $C->$f(); break; case 1: $C->$f( $argv[ 0]); break; case 2: $C->$f( $argv[ 0], $argv[ 1]); break; case 3: $C->$f( $argv[ 0], $argv[ 1], $argv[ 2]); break; case 4: $C->$f( $argv[ 0], $argv[ 1], $argv[ 2], $argv[ 3]); break; case 5: $C->$f( $argv[ 0], $argv[ 1], $argv[ 2], $argv[ 3], $argv[ 4]); break; case 6: $C->$f( $argv[ 0], $argv[ 1], $argv[ 2], $argv[ 3], $argv[ 4], $argv[ 5]); break; }
 	//switch ( count( $argv)) { case 0: $C->$f( $wdir); break; case 1: $C->$f( $wdir, $argv[ 0]); break; case 2: $C->$f( $wdir, $argv[ 0], $argv[ 1]); break; case 3: $C->$f( $wdir, $argv[ 0], $argv[ 1], $argv[ 2]); break; case 4: $C->$f( $wdir, $argv[ 0], $argv[ 1], $argv[ 2], $argv[ 3]); break; case 5: $C->$f( $wdir, $argv[ 0], $argv[ 1], $argv[ 2], $argv[ 3], $argv[ 4]); break; case 6: $C->$f( $wdir, $argv[ 0], $argv[ 1], $argv[ 2], $argv[ 3], $argv[ 4], $argv[ 5]); break; }
 	die();
}
if ( ! isset( $argv) && ( isset( $_GET) || isset( $_POST)) && ( $_GET || $_POST)) { // web API 
	set_time_limit( 0);
	ob_implicit_flush( 1);
	for ( $prefix = is_dir( 'ajaxkit') ? 'ajaxkit/' : ''; ! is_dir( $prefix) && count( explode( '/', $prefix)) < 4; $prefix .= '../'); if ( ! is_file( $prefix . "env.php")) $prefix = '/web/ajaxkit/'; 
	if ( ! is_file( $prefix . "env.php") && ! is_file( 'requireme.php')) die( "\nERROR! Cannot find env.php in [$prefix] or requireme.php in [.], check your environment! (maybe you need to go to ajaxkit first?)\n\n");
	if ( is_file( 'requireme.php')) require_once( 'requireme.php'); else foreach ( explode( ',', ".,$prefix") as $p) foreach ( array( 'functions', 'env') as $k) if ( is_dir( $p) && is_file( "$p/$k.php")) require_once( "$p/$k.php");
	htg( hm( $_GET, $_POST)); $JSONENCODER = 'jsonraw';
	// check for webkey.json and webkey parameter in request
	if ( is_file( 'webkeys.php') && ! isset( $webkey)) die( jsonsend( jsonerr( 'webkey env not set, run [phpwebkey make] first'))); 
	$good = true; if ( is_file( 'webkeys.php')) $good = false; 
	if ( is_file( 'webkeys.php')) foreach ( file( 'webkeys.php') as $v) if ( strpos( $v, $webkey) !== false) $good = true; 
	if ( ! $good) die( jsonsend( jsonerr( 'did not pass the authenticated form of this web API'))); 
	// actions: [wdir] is fixed/predefined  [action] is function name   others are [one,two,three,...]
	$O = new $CLASS( true); // does not pass [types], expects the user to run init() once locally before using it remotely 
	$p = array(); foreach ( ttl( 'one,two,three,four,five') as $k) if ( isset( $$k)) lpush( $p, $$k); $R = array();
	if ( count( $p) == 0) $R = $O->$action();
	if ( count( $p) == 1) $R = $O->$action( $one);
	if ( count( $p) == 2) $R = $O->$action( $one, $two);
	if ( count( $p) == 3) $R = $O->$action( $one, $two, $three);
	if ( count( $p) == 4) $R = $O->$action( $one, $two, $three, $four);
	if ( count( $p) == 5) $R = $O->$action( $one, $two, $three, $four, $five);
	die( jsonsend( $R));
}
if ( isset( $argv) && count( $argv)) { $L = explode( '/', $argv[ 0]); array_pop( $L); if ( count( $L)) chdir( implode( '/', $L)); } // WARNING! Some external callers may not like you jumping to current directory
// for raw input like JSON POST requests
//if ( ( ! isset( $_GET) && ! isset( $_POST)) || ( ! $_GET && ! $_POST)) { $h = @json_decode( @file_get_contents( 'php://input'), true); if ( $h) $_POST = $h; $out = fopen( 'input', 'w'); fwrite( $out, json_encode( $h)); fclose( $out); } 
?>