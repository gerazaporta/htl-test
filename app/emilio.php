<?php 
$root = '/var/admixt';
$site = "https://admixt.com";

#add your personal access token here
$admixt_access_token = 'EAAVbvz1T8lsBABpztJI71UkO3FuBJfnQLRNoQkbrb1NgUYZCd5jqzUtGZBWPgoB0fRQuH4f0RZCvmbZBfOGr2Ckniu3f8wWVzMQKCEi45w20PfkWmdgUNVeRRcNOmddChAf7gcpZBFGBMSHUI75Rtx9vIQ5LAj6mh0HsCBDaOPdvcdDAZBSO7ZA4ZADTWoviCZAgZD';

function Overlays() {
	global $admixt_access_token, $root, $site;

	$preview = (int)$_REQUEST['preview'];
	$force = (int)$_REQUEST['force'];
	$overlay = addslashes($_REQUEST['overlay']);

	$remove_existing = 0;

	$platform = 'local';

	$query = "SELECT o.id, o.code, o.title, o.source_index, o.image_index, o.destination, a.code as advertiser, a.flightplan_id, a.title as advertiser_title, dc.catalog_id, d.catalog_id as destination_catalog_id, dc.network_id, f.data as feed_data, act.access_token
	FROM overlay o
	INNER JOIN advertiser a ON o.advertiser_id = a.id
	INNER JOIN dynamic_catalog dc ON o.source_catalog_id = dc.id
	INNER JOIN dynamic_catalog d ON o.destination_catalog_id = d.id
	LEFT OUTER JOIN dynamic_feed f ON dc.id = f.catalog_id AND f.status = 'A'
	INNER JOIN advertiser_account aa ON a.id = aa.advertiser_id
	INNER JOIN account act ON aa.account_id = act.id AND act.status = 'A' AND dc.network_id = act.network_id
	WHERE a.status = 'A'
	AND (o.code = '{$overlay}' OR ('{$overlay}' = '' AND o.status = 'A'))
	GROUP BY o.id;";
	//$overlays = sql_array($query);

	$overlays = '[
	{
	id: "60",
	code: "60cbd622e381a",
	title: "Rolling Stones Custom Overlay ",
	source_index: "",
	image_index: "7",
	destination: "flightplan",
	advertiser: "5a54e8920edac",
	flightplan_id: "5a54e8920edad",
	advertiser_title: "MeUndies",
	catalog_id: "1699955716920736",
	destination_catalog_id: "2276834259244851",
	network_id: "1",
	feed_data: "{"hour":9,"id":"249374212104808","interval":"HOURLY","interval_count":1,"minute":0,"timezone":"America\/Los_Angeles","url":"https:\/\/admixt.com\/external\/flightplan\/feeds\/1212234210\/1212234210_custom_group_id.tsv"}",
	access_token: null
	}
	]';

	$overlays = json_decode($overlays, 1);

	$counter = 0;

	if (!$preview) echo 'Overlays: ' . count($overlays) . '<br/>';

	foreach ($overlays as $overlay) {

		if ($overlay['feed_data']) $overlay['feed_data'] = json_decode($overlay['feed_data'], 1);

		$query = "SELECT i.code, i.title, i.image, ds.set_id, op.data, ds.filter as set_filter, op.filter as part_filter
		FROM overlay_part op
		LEFT OUTER JOIN dynamic_set ds ON op.set_id = ds.id
		LEFT OUTER JOIN image i ON op.image_id = i.id
		WHERE op.overlay_id = {$overlay['id']}
		ORDER BY op.id; ";
		#$overlay_parts = sql_array($query);

		$overlay_parts = '[{"code":"60cb798e5deae","title":"Dynamic_Overlays_RollingStonesArtboard 1_1","image":"http:\/\/admixt.com\/external\/creative\/5a54e8920edac\/Dynamic_Overlays_RollingStonesArtboard1_1_1623947662.png","set_id":"955774648325530","data":"{\"z\":\"0\",\"lock\":\"\",\"canvas\":{\"h\":\"500\",\"w\":\"500\"},\"product\":{\"h\":\"500\",\"w\":\"500\",\"x\":\"0\",\"y\":\"-20\"},\"overlay\":{\"h\":\"500\",\"w\":\"500\",\"x\":\"0\",\"y\":\"0\"},\"canvas_type\":\"1x1\"}","set_filter":"{\"name\":{\"i_contains\":\"rolling stones\"}}","part_filter":"null"}]';
		$overlay_parts = json_decode($overlay_parts, 1);

		if (!$preview) echo 'Parts: ' . count($overlay_parts) . '<br/>';

		$completed_codes = array();

		$image_directory = "{$overlay['advertiser']}/overlays/{$overlay['code']}";
		$path = "{$root}/external/creative/{$image_directory}";

		if ($overlay['destination'] == 'flightplan') {
		    if (!$overlay['flightplan_id']) {
		    	#send_mail('adMixt Overlay Error <help@admixt.com>', 'zach@admixt.com', "Overlay: {$overlay['advertiser_title']}" , 'Flightplan ID Missing<br/>https://admixt.com?advertiser=' . $overlay['advertiser']);
		    	continue;
		    }

		    switch ($overlay['network_id']) {
		    	default:
		    		$path = "{$root}/external/flightplan/images/{$overlay['flightplan_id']}/additional_image_feeds/{$overlay['destination_catalog_id']}";
		    	break;

		    	case 5:
		    		if ($overlay['image_index'] == '') {
		    			$overlay['image_index'] = 'main';
		    		}
		    		$path = "/var/admixt/external/flightplan/images/{$overlay['flightplan_id']}/pinterest/{$overlay['destination_catalog_id']}/{$overlay['image_index']}";
		    	break;
		    }
		    
		}

		echo 'Path: ' . $path . '<br/>';
		@mkdir("{$path}", 0777, true);

		$existing_images = scandir($path);
		echo 'Existing Images: ' . (count($existing_images) - 2) . '<br/>';

		foreach ($existing_images AS $key => $existing_image) {
			$split_index = strpos($existing_image, '.') ? strpos($existing_image, '.') : -1;
			$split_index = strpos($existing_image, '_hash') ? strpos($existing_image, '_hash') : $split_index;

			$new_key = str_split($existing_image, $split_index);
			$new_key = $new_key[0];
		
			unset($existing_images[$key]);
			
			$new_timestamp = filemtime("{$path}/{$existing_image}");
			if ($new_key) {
				if (!isset($existing_images[$new_key]) || $existing_images[$new_key]['timestamp'] < $new_timestamp) {
					$existing_images[$new_key] = array(
						'filename'  => $existing_image,
						'timestamp' => $new_timestamp
					);
				}
			}
		}

		foreach ($overlay_parts as $overlay_part) {
			echo 'Set ID: ' . $overlay_part['set_id'] . '<br/>';
			$overlay_part['data'] = json_decode($overlay_part['data'], 1);

			$response = overlayPart($overlay, $overlay_part, $path, 0, $existing_images);

			if ($_REQUEST['debug']) echo json_encode_wrapper($response) . '<br/><br/>';
		}
	}
}

function overlayPart($overlay, $overlay_part, $path, $preview = 0, $existing_images) {
	global $root, $admixt_access_token, $sets, $completed_codes;
	$limit = (int)$_REQUEST['limit'];
	$force = (int)$_REQUEST['force'];
	$item_group_ids = array();

	if ($_REQUEST['item_group_ids']) {
		$item_group_ids = explode(',', $_REQUEST['item_group_ids']);
	}

	if (!$preview) {
		echo '<h2>NEW PART</h2>';
	}

	if ($overlay_part['image']) {
		list($overlay_w, $overlay_h) = getimagesize($overlay_part['image']);

		if (!$preview) {
			echo 'Overlay: ' . $overlay_part['image'] . '<br/>';
			echo $overlay_w . ' x ' . $overlay_h . '<br/>';
		}
	}

	$filter = json_decode($overlay_part['part_filter'], 1);

	if ($filter) {
		if (!$filter['and']) $filter = array('and' => array($filter));

		foreach ($filter['and'] as $and) {
			if (!$and['or']) $and = array('or' => array($and));

			foreach ($and['or'] as $or) {
				foreach ($or as $field => $pair) {
					$fields[] = $field;
				}
			}
		}
	}

	$fields = array_values(array_unique($fields));

	$data = $overlay_part['data'];

	if (!$preview) {
		echo 'DATA ORIGINAL: ' . json_encode_wrapper($data) . '<br/>';
	}

	$variants = array();
	$products = array();

	$api_limit = 100;

	if ($limit && $limit < $api_limit) {
		$api_limit = $limit;
	}

	switch ($overlay['network_id']) {
		case 1: 
			if (!$preview) {
				echo 'FB PRODUCT SET<br/>';
			}
			if ($overlay['source_index'] != '') {
				$fields = ['retailer_id','retailer_product_group_id','image_url','additional_image_urls'];
			} else {
				$fields = ['retailer_id','retailer_product_group_id','image_url'];
			}

			if (isset($data['text']['text'])) {
				$fields[] = $data['text']['text'];
			}

			$fields = implode(',', $fields);

			$start = microtime(1);
			$url = "/{$overlay_part['set_id']}/products?fields={$fields}&limit={$api_limit}&access_token={$admixt_access_token}";

			while ($url) {
				$response = facebook($url, 'GET');
				$counter = count($response['data'] );

				if ($response['error']) {
					sleep(1);
					$response = facebook($url, 'GET');
					$counter = count($response['data']);

					if ($response['error']) {
						#send_mail('adMixt Overlay Error <help@admixt.com>', 'zach@admixt.com', "Overlay: {$overlay['advertiser_title']}" , $url . '<br/><br/>' . json_encode_wrapper($response));
					}
				}
				
				$url = '';
				
				if ($counter == $api_limit) {
					$url = $response['paging']['next'];
				}

				$variants = array_merge($variants, $response['data']);

				if ($limit && count($variants) >= $limit) {
					break;
				}
			}

			if (!$preview) {
				echo 'Variants: ' . count($variants) . '<br/>';
			}

			if ($limit) {
			    /** TODO: Ask why make make a fb pagination if we are going just to take the limit again */
				$variants = array_slice($variants, 0, $limit);
			}

			foreach ($variants as $variant) {
				if (stripos($variant['image_url'], '.png') !== false || stripos($variant['image_url'], '.jpg') !== false) {
					if (!$products[$variant['retailer_product_group_id']]) {
						$products[$variant['retailer_product_group_id']] = $variant;
		 				unset($products[$variant['retailer_product_group_id']]['retailer_id']);
					}

					$products[$variant['retailer_product_group_id']]['variants'][] = $variant;
				}
			}
		break;

		case 5: {
			$feed = $overlay['feed_data']['location_config']['full_feed_fetch_location'];

			if (!$preview) {
				echo 'Feed: ' . $feed . '<br/>';
			}

			if (!$sets['products'][$overlay['catalog_id']]) {
				$response = feedProducts($feed, $limit);
				$variant_count = $response['variant_count'];
				$sets['products'][$overlay['catalog_id']] = $response['products'];
				$sets['product_ids'][$overlay['catalog_id']] = $response['product_ids'];
			}

			if (!$preview) {
				echo 'TOTAL PRODUCTS: ' . number_format(count($sets['products'][$overlay['catalog_id']])) . '<br/>';
			}

			foreach ($sets['product_ids'][$overlay['catalog_id']] as $item_group_id => $variants) {
				foreach ($variants as $variant_id) {
					$item_group_ids[$variant_id] = $item_group_id;
				}
			}

			$products = array();

			$overlay_part['set_filter'] = pinterestFilter($overlay_part['set_filter']);

			if ($filter) {
				if (!$preview) {
					echo 'PIN FILTER<br/>';
				}

				$api_limit = 0;
				if ($limit && $limit < $api_limit) {
					$api_limit = $limit;
				}
				foreach ($sets['products'][$overlay['catalog_id']] as $item_group_id => $product) {
					$pass = 0;

					$product_types[$product['product_type']]++;

					foreach ($filter['and'] as $and) {
						if (!$and['or']) $and = array('or' => array($and));

						foreach ($and['or'] as $or) {
							foreach ($or as $field => $pair) {
								foreach ($pair as $operator => $value) {
									switch ($operator) {
										case 'eq': {
											if ($product[$field] == $value) {
												$pass = 1;
											}
											break;
										}
										case 'i_contains': {
											if (stripos($product[$field], $value) > -1) {
												$pass = 1;
											}
											break;
										}
										case 'i_not_contains': {
											if (stripos($product[$field], $value) === false) {
												$pass = 1;
											}
											break;
										}
									}
								}
							}
						}
					}

					if ($pass) {
						$products[$item_group_id] = $product;
					} else {
						$not[$item_group_id] = $product;
					}

					if ($limit && count($products) >= $limit) {
						break;
					}
				}

				if ($debug) echo 'Total Products: ' . number_format(count($sets['products'][$overlay['catalog_id']])) . '<br/>';
				if ($debug) echo 'Filtered Products: ' . number_format(count($products)) . '<br/>';
			} else {
				if (!$preview) {
					echo 'PIN PRODUCT SET<br/>';
				}
				
				$url = "https://api.pinterest.com/v3/catalogs/product_groups/products/{$overlay['catalog_id']}/?filters={$overlay_part['set_filter']}&access_token={$overlay['access_token']}&page_size={$api_limit}";
				
				while ($url) {
					$response = pinterest($url, 'GET');	
					
					if ($debug) echo $url . '<br/>';
					if ($debug) echo 'Count: ' . number_format(count($response['data'])) . '<br/>';
					
					if (!$response['data']) {
						$response['url'] = $url;
						if ($debug) echo json_encode_wrapper($response) . '<br/><br/>';
					}
					$url = '';
					foreach ($response['data'] as $row) {
						$variant_id = substring($row['link'], 'variant=', '&');
						$item_group_id = $item_group_ids[$variant_id];

						#$variant['retailer_id'] = $variant_id;
						$variant['retailer_product_group_id'] = $item_group_id;
						$variant['image_url'] = $row['image_large_url'];
						$variant['id'] = $row['id'];
						$variant['link'] = $row['link'];
						$variant['width'] = $row['image_large_size_pixels']['width'];
						$variant['height'] = $row['image_large_size_pixels']['height'];

						/*if (!$item_group_id) {
							echo $variant_id . ' not found<br/>';
							echo json_encode_wrapper($variant) . '<br/>';
							echo json_encode_wrapper($item_group_ids) . '<br/>';
							die();
						}*/

						$products[$item_group_id] = $variant;
					}

					if ($debug) echo 'Products: ' . number_format(count($products)) . '<br/>';

					if ($response['bookmark']) {
						$url = "https://api.pinterest.com/v3/catalogs/product_groups/products/{$overlay['catalog_id']}/?filters={$overlay_part['set_filter']}&access_token={$overlay['access_token']}&page_size={$api_limit}&bookmark={$response['bookmark']}";
					}

					if ($limit && count($products) >= $limit) {
						break;
					}
				}
			}
			break;
		}
	}

	if (1) {
		$rows = $products;
	} else {
		$rows = $variants;
	}
	
	$duration = microtime(1) - $start;
	$durations['facebook'] = $duration;

	$start = microtime(1);

	if (!$preview) {
		echo 'Products: ' . count($rows) . '<br/>';
	}

	$current_row = 0;
	$duration = microtime(1) - $start;
	$durations['existing'] = $duration;

	foreach ($rows as $row) {
		$pass = 1;
		$data = $overlay_part['data'];
		$overlay_image = $overlay_part['image'];

		if (stripos($overlay_image, '?') > -1) {
			$overlay_image = explode('?', $overlay_image);
			$overlay_image = $overlay_image[0];
		}

		$current_row++;
		if (!$preview) {
			echo 'Row ' . $current_row . ' out of ' . count($rows) . '<br/>';
		}

		unset($row['variants']);

		$response['row'] = $row;

		$hash = md5(json_encode_wrapper($row));
		if ($row['retailer_id']) {
			$code 	  = $row['retailer_id'];
			$filename = "{$row['retailer_id']}_v_{$overlay['image_index']}_hash_{$hash}.png";
			$filename = "{$row['retailer_id']}_v_{$overlay['image_index']}.png";
			$file_key = "{$row['retailer_id']}_v_{$overlay['image_index']}";
			$remove_path_filename = "{$row['retailer_id']}_v_{$overlay['image_index']}_hash_*.png";
		} else {
			$code = $row['retailer_product_group_id'];
			$filename = "{$row['retailer_product_group_id']}_{$overlay['image_index']}_hash_{$hash}.png";
			$filename = "{$row['retailer_product_group_id']}_{$overlay['image_index']}.png";
			$file_key = "{$row['retailer_product_group_id']}_{$overlay['image_index']}";
			$remove_path_filename = "{$row['retailer_product_group_id']}_{$overlay['image_index']}_*.png";

			if ($overlay['network_id'] == 5) {
				$filename = "{$row['retailer_product_group_id']}.png";
				$file_key = "{$row['retailer_product_group_id']}";
				$remove_path_filename = "{$row['retailer_product_group_id']}.png";
			}
		}

		$image_path = "{$path}/{$filename}";
		$remove_path = "{$path}/{$remove_path_filename}";

		if (in_array($code, $completed_codes)) {
			if (!$preview) {
				echo 'Completed<br/>';
			}
		} else {
			$completed_codes[] = $code;

			if (!$row['image_url']) {
				$row['image_url'] = $row['image_link'];
			}

			#let's not worry about the filename for right now
			#if ((!isset($existing_images[$file_key]) || $existing_images[$file_key]['filename'] != $filename) || $force) {
			if (isset($existing_images[$file_key]) && !$force) {
				echo 'Existing: ' . json_encode_wrapper($existing_images[$file_key]) . ' (' . json_encode_wrapper($force) . ')<br/>';
			} else {
				if ($overlay['source_index'] != '') {
					$image_url = $row['additional_image_urls'][$overlay['source_index']];
					if (!$image_url) {
						$image_url = $row['image_url'];
					}
				} else {
					$image_url = $row['image_url'];
				}

				$response['image_url'] = $image_url;

				list($product_w, $product_h, $product_type) = getimagesize($image_url);
				list($overlay_w, $overlay_h, $overlay_type) = getimagesize($overlay_image);

				switch ($data['canvas_type']) {
					case '1x1': {
						$canvas_h = 1080;
						$canvas_w = 1080;
						break;
					}

					case '2x3': {
						$canvas_h = 900;
						$canvas_w = 600;
						break;
					}

					case '9x16': {
						$canvas_h = 1200;
						$canvas_w = 675;
						break;
					}

					case 'overlay': {
						$canvas_h = $overlay_h;
						$canvas_w = $overlay_w;

						$data['canvas']['w'] = $overlay_w;
						$data['canvas']['h'] = $overlay_h;
						break;
					}

					case 'product': {
						$canvas_h = $product_h;
						$canvas_w = $product_w;

						$data['canvas']['w'] = $product_w;
						$data['canvas']['h'] = $product_h;
						break;
					}

					default: {
						$canvas_h = $product_h;
						$canvas_w = $product_w;

						$data['canvas']['w'] = $product_w;
						$data['canvas']['h'] = $product_h;
						break;
					}
				}
				
				$product_ratio 			= number_format($product_w / $product_h, 2);
				$data_product_ratio 	= number_format($data['product']['w'] / $data['product']['h'], 2);
				$canvas_ratio 			= number_format($data['canvas']['w'] / $data['canvas']['h'], 2);

				$response['canvas_ratio'] = $canvas_ratio;
				$response['product_ratio'] = $product_ratio;
				$response['data_product_ratio'] = $data_product_ratio;

				if ($data['lock'] == 'product_canvas' && $product_ratio != $canvas_ratio) {
					$data['canvas']['w'] = $product_w;
					$data['canvas']['h'] = $product_h;
					$data['product']['w'] = $product_w;
					$data['product']['h'] = $product_h;

					$canvas_h = $product_h;
					$canvas_w = $product_w;

					$canvas_ratio = number_format($data['canvas']['w'] / $data['canvas']['h'], 2);
					$overlay_image = '';
				} 

				if (!$data['canvas']) {
					if (!$preview) echo 'No Canvas Data<br/>';
					$data['canvas']['w'] = $product_w;
					$data['canvas']['h'] = $product_h;
				}

				if ($data_product_ratio != $product_ratio && $data['product']) {
					if (!$preview) echo "Product Not Correct Ratio ({$product_w} x {$product_h}) - ({$data_product_ratio} != {$product_ratio})<br/>";
					$data['product']['h'] = $data['product']['h'] / $product_ratio * $data_product_ratio;
				}

				if (!$data['product']) {
					if (!$preview) echo 'No Product Data<br/>';
					$data['product']['w'] = $product_w;
					$data['product']['h'] = $product_h;
					$data['product']['x'] = 0;
					$data['product']['y'] = 0;
					$data_product_ratio = number_format($data['product']['w'] / $data['product']['h'], 2);
				}

				if (($data['product']['w'] + $data['product']['x']) > $data['canvas']['w']) {
					if (!$preview) echo "Product Wider than Canvas - ({$data['product']['w']} + {$data['product']['x']}) > {$data['canvas']['w']}<br/>";
					$ratio = $data['canvas']['w'] / ($data['product']['w'] + $data['product']['x']);
					$data['product']['w'] = $data['product']['w'] * $ratio;
					$data['product']['h'] = $data['product']['h'] * $ratio;
					$data['product']['y'] = $data['product']['y'] / $ratio;
					$data_product_ratio = number_format($data['product']['w'] / $data['product']['h'], 2);
				}

				if (($data['product']['h'] + $data['product']['y']) > $data['canvas']['h']) {
					if (!$preview) echo "Product Taller than Canvas - ({$data['product']['h']} + {$data['product']['y']}) > {$data['canvas']['h']}<br/>";
					$ratio = $data['canvas']['h'] / ($data['product']['h'] + $data['product']['y']);
					$data['product']['w'] = $data['product']['w'] * $ratio;
					$data['product']['h'] = $data['product']['h'] * $ratio;
					$data['product']['x'] = $data['product']['x'] / $ratio;
					$data_product_ratio = number_format($data['product']['w'] / $data['product']['h'], 2);
				}
				
				$opaque = 1;

				switch ($product_type) {
					case 2: {
						$product = imagecreatefromjpeg($image_url);
						if (isset($data['z']) && (int)$data['z'] < 2) {
							#let's just continue since the background wont work
							echo 'Non-transparent: continue<br/>';
							$pass = 0;
							continue;

							#we need to clear out the background because this is a jpg
							/*
							unset($overlay_image);
							$product_h = $overlay_h;
							$product_w = $overlay_w;
							$data['product'] = $data['overlay'];
							*/
						}
						break;
					}
					
					case 3: {
						#even if it's opaque, we still want to load the product on top, right?
						#$opaque = is_opaque_png($image_url);
						$opaque = 0;
						if (!$opaque && $overlay_image && isset($data['z']) && (int)$data['z'] < 2) {
							#Switch overlay and product image so image overlays BG
							$ext = end(explode('.', $overlay_image));

							switch ($ext) {
								case 'jpg':
								case 'jpeg': {
									$product = imagecreatefromjpeg($overlay_image);
									break;
								}
								

								case 'png': {
									$product = imagecreatefrompng($overlay_image);
									break;
								}
								
							}

							echo 'DATA BEFORE SWAP: ' . json_encode_wrapper($data) . '<br/>';
							$temp = $data['product'];
							$temp_h = $product_h;
							$temp_w = $product_w;

							$product_h = $overlay_h;
							$product_w = $overlay_w;
							$overlay_h = $temp_h;
							$overlay_w = $temp_w;

							$data['product'] = $data['overlay'];
							$data['overlay'] = $temp;
							$overlay_image = $image_url;
							echo 'DATA AFTER SWAP: ' . json_encode_wrapper($data) . '<br/>';
						} else {
							$product = imagecreatefrompng($image_url);
						}
						break;
					}
				}
				
				if (!$preview) {
					echo 'Image: ' . $image_url . '<br/>';
					echo 'Type: ' . $product_type . '<br/>';
					echo 'Opaque: ' . json_encode_wrapper($opaque) . '<br/>';
					echo 'Hash JSON: ' . json_encode_wrapper($row) . '<br/>';
				}

				$ratio = $canvas_h / $data['canvas']['h'];

				if (!$preview) {
					echo 'Data: ' . json_encode_wrapper($data) . '<br/>';
					echo 'Ratio: ' . $ratio . '<br/>';
				}

				#if (!$opaque) {
					#overlay image on opaque background
					$start = microtime(1);

					if (!$preview) {
						echo "CANVAS - imagecreatetruecolor({$canvas_w}, {$canvas_h});<br/>";
					}
					$bg = imagecreatetruecolor($canvas_w, $canvas_h);

					$white = imagecolorallocate($bg, 255, 255, 255);
					imagefill($bg, 0, 0, $white);

					#imagecopyresampled(dst_image, src_image, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)					
					if (!$preview) {
						echo "PRODUCT - imagecopyresampled({$bg}, {$product}, {$data['product']['x']} * {$ratio}, {$data['product']['y']} * {$ratio}, 0, 0, {$data['product']['w']} * {$ratio}, {$data['product']['h']} * {$ratio}, {$product_w}, {$product_h});<br/>";
					}

					imagecopyresampled(
					    $bg, $product,
					    $data['product']['x'] * $ratio, $data['product']['y'] * $ratio, #dst
					    0, 0, #src
					    $data['product']['w'] * $ratio, $data['product']['h'] * $ratio, #dst
					    $product_w, $product_h); #src

					#$product = $bg;
					$duration = microtime(1) - $start;
					$durations['opaque'] = $duration;
				#}
				
				if ($overlay_image) {	
					if (!$preview) {
						echo '<h3>Image</h3>';
						echo $overlay_image . '<br/>';
					}

					$start = microtime(1);
					$overlay_image = imagecreatefrompng($overlay_image);

					#$copy_response = imagecopyresampled($product, $overlay_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

					if (!$preview) {
						echo "OVERLAY - imagecopyresampled({$bg}, {$overlay_image}, {$data['overlay']['x']} * {$ratio}, {$data['overlay']['y']} * {$ratio}, 0, 0, {$data['overlay']['w']} * {$ratio}, {$data['overlay']['h']} * {$ratio}, {$overlay_w}, {$overlay_h});<br/>";
					}

					$copy_response = imagecopyresampled(
					    $bg, $overlay_image,
					    ($data['overlay']['x'] * $ratio), ($data['overlay']['y'] * $ratio), #dst
					    0, 0, #src
					    ($data['overlay']['w'] * $ratio), ($data['overlay']['h'] * $ratio), #dst
					    $overlay_w, $overlay_h); #src

					$duration = microtime(1) - $start;
					$durations['overlay'] = $duration;
				}

				if ($data['text']) {
					$text 					= $row[$data['text']['text']];
					$params['text_align'] 	= $data['text']['alignment'];
					$params['text_expand'] 	= $data['text']['expand'];
					$params['font'] 		= $data['text']['font'];
					$params['font_size'] 	= $data['text']['size'];
					$params['transparency'] = (int)$data['text']['transparency'];
					$params['font_color'] 	= colorToArray($data['text']['color']);

					unset($params['box_color']);

					if ($data['text']['background']) {
						$params['box_color'] 	= colorToArray($data['text']['background']);
					}
					$params['box_width'] 	= (int)($data['text']['w'] * $ratio);
					$params['box_height'] 	= (int)($data['text']['h'] * $ratio);
					$params['box_left'] 	= (int)($data['text']['x'] * $ratio);
					$params['box_top'] 		= (int)($data['text']['y'] * $ratio);
					$params['spacing'] 		= 0;
					$params['font_size_max'] = 64;
					$params['preview'] 		= $preview;

					#function imageTextBox($advertiser, $code, $title, $image, $box_width, $box_height, $box_left, $box_top, $box_color, $font, $font_color, $font_size_max, $text, $index) {
					#imageTextBox($advertiser, $platform, $code, $image, $text, $params); 

					if (!$preview) {
						echo 'Params: ' . json_encode_wrapper($params)  . '<br/>';
						echo 'Text: ' . $text . '<br/>';
					}

					if ($text) {
						$start = microtime(1);
						$text_response = imageTextBox($overlay['advertiser'], 'flightplan', $code, $bg, $text, $params);
						$duration = microtime(1) - $start;
						$durations['text'] = $duration;
						$bg = $text_response['image'];

						if (!$preview) {
							echo 'Text Response: ' . var_dump($text_response) . '<br/><br/>';
						}
					}

					flush();
				}

				if ($remove_existing && !$preview) {
					$start = microtime(1);
					$remove = array_map("unlink", glob($remove_path));
					$duration = microtime(1) - $start;
					$durations['remove'] = $duration;

					echo 'Remove: ' . json_encode_wrapper($remove) . '<br/><br/>';
				}

				$response['image_path'] = $image_path;

				if ($pass) {
					$write = imagepng($bg, $image_path, 9);

					if ($write) {
						$existing_images[$file_key] = [
							'filename'  => $filename,
							'timestamp' => time()
						];
					}

					if (!$preview) {
						echo 'IMAGE: ' . $image_path . '<br/>';
					}

					$image_src = str_ireplace('/var/admixt', $site, $image_path);
					$image_srcs[] = $image_src;

					if (!$preview) {
						echo '<img src="' . $image_src . '" height="200"/><br/>';

						echo 'BG: ' . var_dump($bg) . '<br/>';
						echo 'OVERLAY: ' . var_dump($overlay_image) . '<br/>';
						echo 'WRITE: ' . var_dump($write) . '<br/>';
						echo '<br/>Durations:<br/>' . json_encode_wrapper($durations) . '<br/>';
						#echo 'Response:<br/>' . json_encode_wrapper($response) . '<br/>';

						flush();
						echo '<br/><br/>';
					}
				}
			}
		}
	}

	$response['image_srcs'] = $image_srcs;
	$response['overlay_part'] = $overlay_part;
	$response['overlay'] = $overlay;
	$response['product_types'] = $product_types;
	$response['feed'] = $feed;
	$response['products'] = count($sets['products'][$overlay['catalog_id']]);
	$response['variants'] = $variant_count;
	return $response;
}

function imageTextBox($advertiser, $platform, $code, $image, $text, $params) {
	global $root, $site;

	extract($params);

    switch($font) {
    	case 'Vito_Black': 		$font_path = $root . '/external/fonts/Vito_Black.otf'; break;
		case 'Vito_Bold': 		$font_path = $root . '/external/fonts/Vito_Bold.otf'; break;
		case 'Vito_Light': 		$font_path = $root . '/external/fonts/Vito_Light.otf'; break;
		case 'Vito_Medium': 	$font_path = $root . '/external/fonts/Vito_Medium.otf'; break;
		case 'Vito_Wide':  		$font_path = $root . '/external/fonts/BoldVito_Wide.otf'; break;
		case 'Vito_Wide': 		$font_path = $root . '/external/fonts/Vito_Wide.otf'; break;
		case 'Vito': 			$font_path = $root . '/external/fonts/Vito.otf'; break;

		case 'DejaVuSans': 	$font_path 	= $root . '/external/fonts/DejaVuSans.ttf'; break;
    	case 'Playfair': 	$font_path 	= $root . '/external/fonts/playfairdisplay-black.ttf'; break;

    	case 'ProximaNova-Bold': 		$font_path 	= $root . '/external/fonts/ProximaNova-Bold.ttf'; break;
    	case 'ProximaNova-Semibold': 	$font_path 	= $root . '/external/fonts/ProximaNova-Semibold.ttf'; break;
    	case 'ProximaNova-Regular': 	$font_path 	= $root . '/external/fonts/ProximaNova-Regular.ttf'; break;

    	case 'SharpSans-Bold': 	$font_path 	= $root . '/external/fonts/SharpSans-Bold.ttf'; break;

    	default: 				$font_path 	= $root . '/external/fonts/' . $font; break;
    }

    $box_area = (int)($box_width * $box_height);
	$box_ratio = (int)$box_width / (int)$box_height;

    if (!$preview) {
	    echo 'advertiser = ' . json_encode($advertiser) . '<br/>';
		echo 'platform = ' . json_encode($platform) . '<br/>';
		echo 'code = ' . json_encode($code) . '<br/>';
		echo 'image = ' . var_dump($image) . '<br/>';
		echo 'box_width = ' . json_encode($box_width) . '<br/>';
		echo 'box_height = ' . json_encode($box_height) . '<br/>';
		echo 'box_left = ' . json_encode($box_left) . '<br/>';
		echo 'box_top = ' . json_encode($box_top) . '<br/>';
		echo 'transparency = ' . json_encode($transparency) . '<br/>';
		echo 'box_color = ' . json_encode($box_color) . '<br/>';
		echo 'font_path = ' . $font_path . '<br/>';
		echo 'font_color = ' . json_encode($font_color) . '<br/>';
		echo 'font_size = ' . json_encode($font_size) . '<br/>';
		echo 'font_size_max = ' . json_encode($font_size_max) . '<br/>';
		echo 'text = ' . $text . '<br/>';
		echo 'spacing = ' . json_encode($spacing) . '<br/>';
		echo 'box_area = ' . json_encode($box_area) . '<br/>';
		echo 'box_ratio = ' . json_encode($box_ratio) . '<br/>';
	}

	$text = remove_emoji($text);
	$text_length = strlen($text);
	
    $words = explode(' ', $text);

	$sample_token_length = 0;
	$sample_token = null;

	foreach ($words as $word) {
		if(strlen($word) > $max_word_length) {
			$max_word_length = strlen($word);
			$max_word = $word;
		}
	}

	if ($font_size) {
		$font_multiplier = $box_width / 270;
		$font_size = $font_size * $font_multiplier;
	}

	$p = imagettfbbox($font_size, 0, $font_path,  $text);
	$full_txt_width = $p[2] - $p[0];
	$full_txt_height = ($p[1] - $p[7]);
	$letter_width = $full_txt_width / $text_length;
	$max_line_length = (int)(($box_width * 0.75) / $letter_width);

	$text_area = $full_txt_width * $full_txt_height;

	if (!$preview) {
		echo 'full_txt_width = ' . json_encode($full_txt_width) . '<br/>';
		echo 'full_txt_height = ' . json_encode($full_txt_height) . '<br/>';
		echo 'text_area = ' . json_encode($text_area) . '<br/>';
		echo 'letter_width = ' . json_encode($letter_width) . '<br/>';

		echo 'max_lines = ' . json_encode($max_lines) . '<br/>';
		echo 'max_line_length = ' . json_encode($max_line_length) . '<br/>';
	}

	$max_line_length = max(strlen($max_word), $max_line_length);

	$wrapped_text = wordwrap($text, $max_line_length, PHP_EOL);
	$line_tokens = explode(PHP_EOL, $wrapped_text);

	foreach ($line_tokens as $line_token) {
		$max_line_chars = max($max_line_chars, strlen($line_token));
	}

	$box_height_multiplier = 0.7;
	$avg_text_height_multiplier = 1.1;

	$tall_letters = array('g', 'j', 'q', 'p', 'y', 'f', 'J', 'Q');

	if (count($line_tokens) == 1) $box_height_multiplier = 0.5;
	if (strpos($text, $tall_letters) > -1) $avg_text_height_multiplier = 1.3;

	$txt_max_width = intval(0.8 * $box_width);
	$txt_max_height = intval(($box_height_multiplier * $box_height));

	$text_width_max = 0;

	if (!$font_size) {
		$font_size = 1;
		do {
			$font_size++;
			$p = imagettfbbox($font_size, 0, $font_path,  $wrapped_text);

			$txt_width = $p[2] - $p[0];
			$txt_width = $txt_width + ($max_line_chars * $spacing);
			$txt_height = ($p[1] - $p[7]);

			$text_width_max = max($txt_width, $text_width_max);
		} while ($txt_width <= $txt_max_width && $txt_height <= $txt_max_height && $font_size <= $font_size_max && $font_size < 100);
	} else {
		$text_width_max = 10000;

		#while (($text_width_max * 1.2) > $box_width && $font_size >= 12) {
			#$font_size = $font_size - 2;
			$p = imagettfbbox($font_size, 0, $font_path,  $wrapped_text);

			$txt_width = $p[2] - $p[0];
			$txt_width = $txt_width + ($max_line_chars * $spacing);
			$txt_height = ($p[1] - $p[7]);

			if (!$preview) {
				echo 'txt_width = ' . $txt_width . '<br/>';
				echo 'font size = ' . $font_size . '<br/>';
			}
			$text_width_max = $txt_width;
		#}
	}

	if (!$preview) {
		echo 'lines = ' . count($line_tokens) . '<br/>';
		echo 'txt_height = ' . $txt_height . '<br/>';
		echo 'max = ' . $text_width_max . '<br/>';
	}

	list($width, $height, $type, $attr) = getImageSize($image);

	if (!$preview) {
		echo 'Background<br/>';
		echo 'Width: ' . $width . '<br/>';
		echo 'Height: ' . $height . '<br/>';
		echo 'Type: ' . $type . '<br/>';
		echo 'Attr: ' . $attr . '<br/><br/>';
	}

	if ($box_left < 0) {
		$box_left = $width + $box_left;
	}

	#echo 'Expand: ' . $text_expand . '<br/><br/>';
	
	switch ($text_expand) {
		case 'up':
			if (!$preview) {
				echo 'Box Top: ' . $box_top . '<br/>';
				echo 'Box Height: ' . $box_height . '<br/>';
			}
			
			$box_top = ($box_top - $box_height) + ($txt_max_height * 2);
			$box_height = $txt_max_height * 2;

			if (!$preview) {
				echo 'New Box Top: ' . $box_top . '<br/>';
				echo 'New Box Height: ' . $box_height . '<br/>';
			}
		break;

		case 'down':
			$box_height = $txt_max_height * 2;
		break;

		case 'updown':
			if (!$preview) {
				echo 'Box Top: ' . $box_top . '<br/>';
				echo 'Box Height: ' . $box_height . '<br/>';
			}

			$box_top = $box_top + ($box_height/2);
			$box_height = $txt_height + ($font_size * 2);
			$box_top = $box_top - ($box_height/2);
			
			if (!$preview) {
				echo 'New Box Top: ' . $box_top . '<br/>';
				echo 'New Box Height: ' . $box_height . '<br/>';
			}
		break;

		case 'left':
			if (!$preview) {
				echo 'Box Width: ' . $box_width . '<br/>';
				echo 'Text Max: ' . $text_width_max . '<br/>';
			}

			$right_margin = $width - $box_width - $box_left;
			$box_width = (int)($text_width_max * 1.2);
			$box_left = $width - $right_margin - $box_width;
			
			if (!$preview) {
				echo 'Right Margin: ' . $right_margin . '<br/>';
				echo 'New Box Left: ' . $box_left . '<br/>';
				echo 'New Box Width: ' . $box_width . '<br/><br/>';
			}
		break;

		case 'right':
			$box_width = (int)($text_width_max * 1.2);
		break;
	}

	if ($text_width_max / $box_width < 0.5) {
		#DISABLED because doing this shrinks the box to fit the text. Maybe turn back on, based on a setting.
		#if ($box_left + $box_width == $width) {
		#	$box_left = $width - ($text_width_max * 1.8);
		#}
		#$box_width = $text_width_max * 1.8;
	}

	$max_txt_width = $txt_width;
	$avg_text_height = count($line_tokens) > 1 ? $txt_height / count($line_tokens) : $txt_height;
	$avg_text_height = $avg_text_height * $avg_text_height_multiplier;
	$total_txt_vertical_height = $avg_text_height * count($line_tokens);

	$rectangle  = imagecreatetruecolor($box_width, $box_height);
	imagealphablending($rectangle, false);
	imagesavealpha($rectangle, true);

	if ($box_color) {
		$bg_fill    = imagecolorallocatealpha($rectangle, $box_color['r'], $box_color['g'], $box_color['b'], $box_color['t']);
	} else {
		$bg_fill    = imagecolorallocatealpha($rectangle, 255, 255, 255, 127);
		#$bg_fill    = imagecolorallocatealpha($rectangle, 101, 56, 231, 0);
	}
	$white      = imagecolorallocatealpha($rectangle, $font_color['r'], $font_color['g'], $font_color['b'], $box_color['t']);
	
	imagealphablending($rectangle, true);

	imagefill($rectangle, 0, 0, $bg_fill);

	foreach($line_tokens as $line_num => $line) {
		$p = imagettfbbox($font_size, 0, $font_path, $line);

		$txt_width = $p[2] - $p[0];
		$txt_width = $txt_width + (strlen($line) * $spacing);
		$txt_height = ($p[1] - $p[7]);

		switch ($text_align) {
			case 'left':
				$x = 20;
			break;

			case 'right':
				$x = (int)(($box_width - $txt_width) - 30);
			break;

			default: 
				$x = (int)(($box_width - $txt_width) / 2);	
			break;
		}
		
		$y = ($box_height - $total_txt_vertical_height) / 2 + ($avg_text_height + ($line_num * $avg_text_height));

		if (!$preview) {
			echo 'Line: ' . $line . ', box width: ' . $box_width . ', text width: ' . $txt_width . '<br/>';
			echo "imagettftext($rectangle, font_size = $font_size, angle = 0, x = $x, y = $y, font_color = $white, font = $font_path, line = $line);<br/>";
		}
		$response = imagettftextSp($rectangle, $font_size, 0, $x, $y - ($box_height * 0.05), $white, $font_path, $line, $spacing);
		
		if (!$preview) {
			echo 'imagettftextSp: ' . json_encode($response) . '<br/><br/>';
		}
	}

	if ($magick) {
		$lines = implode(PHP_EOL, $line_tokens);

		$box_color = "rgb({$box_color['r']},{$box_color['g']},{$box_color['b']})";
		$font_color = "rgb({$font_color['r']},{$font_color['g']},{$font_color['b']})";
		$emoji_font = "/var/admixt/external/fonts/AppleColorEmoji.ttf";

		$img = new Imagick();
		$img->setBackgroundColor(new ImagickPixel($box_color));
		#$img->setFont($emoji_font);
		$img->setFont($font_path);
		$img->setPointSize($font_size);
		#$img->colorizeImage($font_color, 1.0);
		$img->setGravity( Imagick::GRAVITY_CENTER );

		//Pango code for Hello World!
		$img->newPseudoImage($box_width, $box_height, "pango:{$lines}");

		$img->setImageFormat('png');

		$magick = $img->writeImage($path);

		#var_dump($magick) . '<br/><br/>';
	} else {

		/*pass in image resource instead
		var_dump($image) . '<br/><br/>';

		switch ($type) {
	        case IMAGETYPE_GIF: 	$image = imagecreatefromgif($image);	break;
	        case IMAGETYPE_JPEG: 	$image = imagecreatefromjpeg($image); 	break;
	        case IMAGETYPE_PNG: 	$image = imagecreatefrompng($image); 	break;
	    }

	    var_dump($image) . '<br/><br/>';
	    */

		#imagecopyresampled(dst_image, src_image, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
	   	
	    if ($transparency) {
	    	imagealphablending($rectangle, false);

			$filter = imagefilter($rectangle, IMG_FILTER_COLORIZE, 0, 0, 0, 127 * ($transparency/100));

			#$filter = imagefilter($image, IMG_FILTER_COLORIZE, 0, 0, 0, 127 * 0.5);

			#echo '<br>Filter: ' . ($transparency/100) . '<br/>' . var_dump($filter) . '<br/><br/>';
		}

		#with this off, the transparency works, but how does it impact overlay images with transparent BGs?
		#imagealphablending($image, false);

		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		#echo "<br/>$image, $rectangle, left = {$box_left}, top = {$box_top}, 0, 0, width = {$box_width}, height = {$box_height}, width = {$box_width}, height = {$box_height}))<br/>";

	    $composite = imagecopyresampled($image, $rectangle, $box_left, $box_top, 0, 0, $box_width, $box_height, $box_width, $box_height);
		
		if (!$preview) {
			echo 'composite: ' . var_dump($composite) . '<br/><br/>';
		}
	}

    ini_set('display_errors', 0);
    $response = array();
    
    $response['composite'] = $composite;
    $response['src'] = $src;
    $response['path'] = $path;
    $response['image'] = $image;

    return $response;
}

function imagettftextSp($image, $size, $angle, $x, $y, $color, $font, $text, $spacing = 0) {
    if ($spacing == 0) {
        imagettftext($image, $size, $angle, $x, $y, $color, $font, $text);
    } else {
        $temp_x = $x;
        $temp_y = $y;
        //to avoid special char problems
        $char_array = preg_split('//u',$text, -1, PREG_SPLIT_NO_EMPTY);
        foreach($char_array as $char) {
            $response = imagettftext($image, $size, $angle, $temp_x, $temp_y, $color, $font, $char);
			$bbox = imagettfbbox($size, 0, $font, $char);
            $temp_x += cos(deg2rad($angle)) * ($spacing + ($bbox[2] - $bbox[0]));
            $temp_y -= sin(deg2rad($angle)) * ($spacing + ($bbox[2] - $bbox[0]));
        }
    }
}

function json_encode_wrapper($array, $pretty_print = false) {
	if($pretty_print) {
		return json_encode($array, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR);
	}

	return json_encode($array, JSON_PARTIAL_OUTPUT_ON_ERROR);
}