<?php

/**
 * Get the settings
 *
 * @param string $key Only get a specific key.
 * @return mixed
 */
function siteorigin_panels_setting($key = ''){

	// kill the added options for performance, set
	// setting manually here
	$settings = array(
		'home-page' => false,
		'home-page-default' => false,
		'post-types' => array( 'page', 'post', 'core-casestudy' ),
		'bundled-widgets' => true,
		'responsive' => true,
		'mobile-width' => 767,
		'margin-bottom' => 0,
		'margin-sides' => 0,
		'affiliate-id' => false,
		'copy-content' => true,
		'animations' => false,
		'inline-css' => true,
	);

	if ( $key && array_key_exists( $key, $settings ) ) 
		return $settings[$key];

	// Filter these settings
	return apply_filters( 'siteorigin_panels_settings', $settings );
}

/**
 * Display the field for selecting the post types
 *
 * @param $args
 */
function siteorigin_panels_options_field_post_types($args){
	$panels_post_types = siteorigin_panels_setting('post-types');

	$all_post_types = get_post_types( array('_builtin' => false) );
	$all_post_types = array_merge(array('page' => 'page', 'post' => 'post'), $all_post_types);
	unset($all_post_types['ml-slider']);

	foreach($all_post_types as $type){
		$info = get_post_type_object($type);
		if(empty($info->labels->name)) continue;
		$checked = in_array(
			$type,
			$panels_post_types
		);
		
		?>
		<label>
			<input type="checkbox" name="siteorigin_panels_post_types[<?php echo esc_attr($type) ?>]" value="<?php echo esc_attr($type) ?>" <?php checked($checked) ?> />
			<?php echo esc_html($info->labels->name) ?>
		</label><br/>
		<?php
	}
	
	?><p class="description"><?php _e('Post types that will have the page builder available', 'siteorigin-panels') ?></p><?php
}

/**
 * Display the fields for the other settings.
 *
 * @param $args
 */
function siteorigin_panels_options_field_display($args){
	$settings = siteorigin_panels_setting();
	switch($args['type']) {
		case 'responsive' :
		case 'copy-content' :
		case 'animations' :
		case 'inline-css' :
		case 'bundled-widgets' :
			?><label><input type="checkbox" name="siteorigin_panels_display[<?php echo esc_attr($args['type']) ?>]" <?php checked($settings[$args['type']]) ?> /> <?php _e('Enabled', 'siteorigin-panels') ?></label><?php
			break;
		case 'margin-bottom' :
		case 'margin-sides' :
		case 'mobile-width' :
			?><input type="text" name="siteorigin_panels_display[<?php echo esc_attr($args['type']) ?>]" value="<?php echo esc_attr($settings[$args['type']]) ?>" class="small-text" /> <?php _e('px', 'siteorigin-panels') ?><?php
			break;
	}

	if(!empty($args['description'])) {
		?><p class="description"><?php echo esc_html($args['description']) ?></p><?php
	}
}

/**
 * Check that we have valid post types
 *
 * @param $types
 * @return array
 */
function siteorigin_panels_options_sanitize_post_types($types){
	if(empty($types)) return array();
	$all_post_types = get_post_types(array('_builtin' => false));
	$all_post_types = array_merge(array('post' => 'post', 'page' => 'page'), $all_post_types);
	foreach($types as $type => $val){
		if(!in_array($type, $all_post_types)) unset($types[$type]);
		else $types[$type] = !empty($types[$type]);
	}
	
	// Only non empty items
	return array_keys(array_filter($types));
}

/**
 * Sanitize the other options fields
 *
 * @param $vals
 * @return mixed
 */
function siteorigin_panels_options_sanitize_display($vals){
	foreach($vals as $f => $v){
		switch($f){
			case 'inline-css' :
			case 'responsive' :
			case 'copy-content' :
			case 'animations' :
			case 'bundled-widgets' :
				$vals[$f] = !empty($vals[$f]);
				break;
			case 'margin-bottom' :
			case 'margin-sides' :
			case 'mobile-width' :
				$vals[$f] = intval($vals[$f]);
				break;
		}
	}
	$vals['responsive'] = !empty($vals['responsive']);
	$vals['copy-content'] = !empty($vals['copy-content']);
	$vals['animations'] = !empty($vals['animations']);
	$vals['inline-css'] = !empty($vals['inline-css']);
	$vals['bundled-widgets'] = !empty($vals['bundled-widgets']);
	return $vals;
}