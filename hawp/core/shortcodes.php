<?php
// ------------------------------------------
// Theme shortcode functions.
// ------------------------------------------

if (!defined('ABSPATH')) exit();

if (!class_exists('Hawp_Theme_Shortcodes')):

class Hawp_Theme_Shortcodes {

	/**
	 * Constructor.
	 */
	public function setup() {
		add_shortcode('home', [$this, 'relative_home']);
		add_shortcode('home_url', [$this, 'relative_home']);
		add_shortcode('uploads', [$this, 'relative_uploads']);
		add_shortcode('theme', [$this, 'relative_stylesheet']);
		add_shortcode('site_title', [$this, 'relative_site_title']);
		add_shortcode('logo', [$this, 'shortcode_logo']);
		add_shortcode('svg', [$this, 'shortcode_svg']);
		add_shortcode('content_cta', [$this, 'shortcode_content_cta']);
		add_shortcode('sitemap', [$this, 'shortcode_sitemap']);
		add_shortcode('menu', [$this, 'shortcode_menu']);
		add_shortcode('year', [$this, 'shortcode_year']);
		add_shortcode('custom_loop', [$this, 'shortcode_custom_loop']);
		add_filter('the_content', [$this, 'do_custom_loop'], 2);
	}

	/**
	 * Shortcode: relative home url.
	 *
	 * [home_url]
	 */
	public function relative_home($atts) {
		$atts = shortcode_atts([
			'id' => ''
		], $atts);

		if ($atts['id'] && get_permalink($atts['id'])) {
			$result = esc_url(get_permalink($atts['id']));
		} else {
			$result = home_url();
		}
		return $result;
	}

	/**
	 * Shortcode: relative uploads url.
	 *
	 * [uploads]
	 */
	public function relative_uploads($atts) {
		$atts = shortcode_atts([
			'id' => '',
			'svg' => 0,
		], $atts);

		if ($atts['id'] && wp_get_attachment_url($atts['id'])) {
			$result = esc_url(wp_get_attachment_url($atts['id']));
		} else {
			$result = wp_get_upload_dir()['baseurl'];
		}

		if ($atts['id'] && $atts['svg'] == true) {
			$result = file_get_contents(esc_url(wp_get_attachment_url($atts['id'])));
		}

		return $result;
	}

	/**
	 * Shortcode: relative stylesheet url.
	 *
	 * [theme]
	 */
	public function relative_stylesheet() {
		return get_stylesheet_directory_uri();
	}

	/**
	 * Shortcode: Get the site name.
	 *
	 * [site_name]
	 */
	public function relative_site_title() {
		if (function_exists('get_bloginfo')) {
			$result = get_bloginfo('name');
			return $result;
		} else {
			return '';
		}
	}

	/**
	 * Shortcode: [logo].
	 */
	public function shortcode_logo($atts) {
		$atts = shortcode_atts([
			'format' => 'link',
			'class' => '',
			'size' => 'full',
		], $atts);

		$format = [
			'link' => '<a href="'.home_url('/').'" rel="home" class="'.$atts['class'].'">%s</a>',
			'wrap' => '<span class="'.$atts['class'].'">%s</span>',
			'raw' => '%s',
		];
		if (!array_key_exists($atts['format'], $format)) {
			$atts['format'] = 'wrap';
		}

		$img = '';
		$logo = get_theme_option('logo');
		if (!empty($logo)) {
			$img = wp_get_attachment_image($logo, $atts['size'], false, [
				'class' => (!empty($atts['class']) ? $atts['class'].'-img' : ''),
				'alt' => get_bloginfo('name'),
			]);
		}
		return sprintf($format[$atts['format']], $img);
	}

	/**
	 * Shortcode: svg
	 *
	 * @todo	Loop throgh the svgs using a foreach as key
	 *			val loop on the repeater object.
	 *
	 * [svg id=""]
	 */
	public function shortcode_svg($atts) {
		$atts = shortcode_atts([
			'id' => '',
		], $atts);

		$args = [
			'id' => intval($atts['id']-1),
		];

		$keys = [
			'label' => 'options_'.get_theme_option_prefix().'svgs_'.($args['id']).'_'.get_theme_option_prefix().'label',
			'svg' => 'options_'.get_theme_option_prefix().'svgs_'.($args['id']).'_'.get_theme_option_prefix().'svg',
		];

		$vals = [
			'class' => get_option($keys['label']) ? 'hm-svg-'.preg_replace('/\s*/', '', strtolower(get_option($keys['label']))) : '',
			'svg' => get_option($keys['svg']),
		];

		$result = '<span class="hm-svg hm-svg-id-'.$args['id'].' '.$vals['class'].'">'.$vals['svg'].'</span>';

		return $result;
	}

	/**
	 * Shortcode: [content_cta].
	 *
	 * Simple cta sentence for the bottom of content pages.
	 */
	public function shortcode_content_cta($atts) {
		$content = get_theme_option('content_cta');
		if (empty($content)) {
			$content = '<p>Call [company] at [phone] for more information or to schedule an appointment.</p>';
		}
		return apply_filters('content_cta', do_shortcode($content));
	}

	/**
	 * Shortcode: [sitemap].
	 *
	 * Returns sitemap for any post type.
	 */
	public function shortcode_sitemap($atts) {
		$atts = shortcode_atts([
			'post_type' => 'page',
			'orderby' => 'title',
			'order' => 'ASC',
			'exclude' => '',
			'show_title' => '',
			'wrapper_class' => '',
			'select' => '',
			'select_placeholder' => '',
		], $atts);

		$posts = get_posts([
			'post_type' => $atts['post_type'],
			'orderby' => $atts['orderby'],
			'order' => $atts['order'],
			'exclude' => $atts['exclude'],
			'posts_per_page' => -1,
		]);

		$args = [
			'select_placeholder' => ($atts['select_placeholder'] ? $atts['select_placeholder'] : 'Choose a '.$atts['post_type'].'...'),
		];

		$result = '';
		$result .= ($atts['show_title'] != 'false' ? '<h2>'.get_post_type_object($atts['post_type'])->labels->name.'</h2>' : '');
		$result .= '<div class="hm-sitemap-wrapper hm-sitemap-wrapper-'.preg_replace('/\s+/', '-', strtolower($atts['post_type'])).' '.$atts['wrapper_class'].'">';
		if ($atts['select'] == 'true') { // Select option
			$result .= '<select class="hm-sitemap hm-sitemap-select hm-sitemap-'.preg_replace('/\s+/', '-', strtolower($atts['post_type'])).'" onchange="if (this.value) window.location.href=this.value">';
			$result .= '<option value="">'.$args['select_placeholder'].'</option>';
			foreach ($posts as $post) {
				$result .= '<option value="'.get_the_permalink($post->ID).'">'.$post->post_title.'</option>';
			}
			$result .= '</select>';
		} else { // Unordered list
			$result .= '<ul class="hm-sitemap hm-sitemap-'.preg_replace('/\s+/', '-', strtolower($atts['post_type'])).'">';
			foreach ($posts as $post) {
				$result .= '<li><a href="'.get_the_permalink($post->ID).'" rel="bookmark" class="hm-sitemap-item">'.$post->post_title.'</a></li>';
			}
			$result .= '</ul>';
		}
		$result .= '</div>';

		return $result;
	}

	/**
	 * Shortcode: [menu]
	 *
	 * Returns a menu based on its name.
	 */
	public function shortcode_menu($atts) {
		$atts = shortcode_atts([
			'name' => '',
			'container_class' => '',
			'container_id' => '',
			'menu_class' => '',
			'wrapper_class' => '', // keep for backward compatibility, use container_class
		], $atts);

		$menu_args = [
			'menu' => !is_numeric($atts['name']) ? preg_replace('/\s+/', '-', strtolower($atts['name'])) : $atts['name'],
			'container' => 'nav',
			'echo' => false,
		];
		if (!empty($atts['container_class'])) {
			$menu_args['container_class'] = $atts['container_class'];
		} elseif ($atts['wrapper_class']) { // back compat for if 'wrapper_class' attr is used
			$menu_args['container_class'] = $atts['wrapper_class'];
		}
		if (stripos($atts['name'], 'social') !== false && empty($atts['menu_class'])) { // back compat for if 'social' is used in the 'name' attr
			$menu_args['container_class'] = !empty($menu_args['container_class']) ? $menu_args['container_class'].' '.strtolower($atts['name']).'-navigation' : strtolower($atts['name']).'-navigation';
		}
		if (!empty($atts['container_id'])) {
			$menu_args['container_id'] = $atts['container_id'];
		}
		if (!empty($atts['menu_class'])) {
			$menu_args['menu_class'] = $atts['menu_class'];
		}

		$result = wp_nav_menu($menu_args);

		return $result;
	}

	/**
	 * Shortcode: [year].
	 */
	public function shortcode_year($atts) {
		$atts = shortcode_atts([
			'since' => '',
		], $atts);

		if ($atts['since']) {
			$result = date('Y') - floatval($atts['since']);
		} else {
			$result = date('Y');
		}

		return $result;
	}

	/**
	 * Shortcode: [custom_loop].
	 */
	public function shortcode_custom_loop($atts, $content) {
		$atts = shortcode_atts([
			'type'     => 'post',
			'id'       => '',
			'cat_type' => 'category_name',
			'cat'      => '',
			'meta_key' => '',
			'include'  => '',
			'exclude'  => '',
			'num'      => '-1',
			'orderby'  => 'date title',
			'order'    => 'DESC',
			'status'   => 'publish',
			'size'     => 'medium',
		], $atts);

		$atts['include'] = !empty($atts['include']) ? array_map('trim',explode(',', $atts['include'])) : $atts['id'];
		$atts['exclude'] = !empty($atts['exclude']) ? array_map('trim',explode(',', $atts['exclude'])) : '';

		$args = [
			'post_type'        => $atts['type'],
			$atts['cat_type']  => $atts['cat'],
			'meta_key'         => $atts['meta_key'],
			'posts_per_page'   => $atts['num'],
			'include'          => $atts['include'],
			'exclude'          => $atts['exclude'],
			'orderby'          => $atts['orderby'],
			'order'            => $atts['order'],
			'post_status'      => $atts['status'],
		];

		$posts = get_posts($args);

		$result = '';
		$counter = 0;
		foreach ($posts as $post) {
			$counter++;
			$vals['ID'] = $post->ID;
			$vals['post_author'] = $post->post_author;
			$vals['post_title'] = $post->post_title;
			$vals['post_content'] = apply_filters('the_content', $post->post_content);
			$vals['post_excerpt'] = apply_filters('the_excerpt', $post->post_excerpt);
			$vals['post_excerpt_content'] = apply_filters('the_excerpt', $post->post_excerpt) ?: wp_trim_words($post->post_content);
			$vals['post_date'] = get_the_date('', $post->ID);
			$vals['permalink'] = get_the_permalink($post->ID);
			$vals['thumbnail'] = get_the_post_thumbnail($post->ID, $atts['size']);
			$vals['thumbnail_url'] = get_the_post_thumbnail_url($post->ID, $atts['size']);
			$item = $content;
			$item = str_ireplace('{counter}', $counter, $item);
			preg_match_all('/\{([A-Z][A-Z0-9_-]*)([^}]*)\}(.*?)\{\/\1\}/is',$item,$matches);
			for ($i=0;$i<=count($matches[0]);$i++) {
				if (array_key_exists($matches[1][$i],$vals)) {
					$val = $vals[$matches[1][$i]];
				} else {
					$val = get_post_meta($post->ID, $matches[1][$i], true);
				}
				if ($val) {
					$item = str_ireplace($matches[0][$i], $matches[3][$i], $item);
				} else {
					$item = str_ireplace($matches[0][$i], '', $item);
				}
			}
			preg_match_all('/\{([A-Z][A-Z0-9_-]*)([^}]*)\}/is',$item,$matches);
			for ($i=0;$i<count($matches[0]);$i++) {
				if (array_key_exists($matches[1][$i],$vals)) {
					$val = $vals[$matches[1][$i]];
				} else {
					$val = get_post_meta($post->ID, $matches[1][$i], true);
				}
				$item = str_ireplace($matches[0][$i], $val, $item);
			}
			$result.= $item;
		}
		return $result;
	}

	/**
	 * Search content for custom_loop and filter it through hooks.
	 */
	public function do_custom_loop($content='') {
		preg_match_all('/\[custom_loop([^\]]*)\](.*?)\[\/custom_loop\]/is', $content, $matches);
		for ($i=0;$i<count($matches[0]);$i++) {
			$content = str_replace($matches[0][$i], do_shortcode($matches[0][$i]), $content);
		}
		return $content;
	}
}

/**
 * Custom loop function.
 */
function do_custom_loop($content) {
	return do_shortcode(hawp_theme_shortcodes()->do_custom_loop($content));
}

/**
 * Initialize Hawp_Theme_Shortcodes class with a function.
 */
function hawp_theme_shortcodes() {
	global $hawp_theme_shortcodes;

	// Instantiate only once.
	if (!isset($hawp_theme_shortcodes)) {
		$hawp_theme_shortcodes = new Hawp_Theme_Shortcodes();
		$hawp_theme_shortcodes->setup();
	}
	return $hawp_theme_shortcodes;
}
hawp_theme_shortcodes();

endif; // class_exists check
