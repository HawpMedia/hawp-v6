<?php
// ------------------------------------------
// Theme utility/helper functions.
// ------------------------------------------

if (!defined('ABSPATH')) exit();

/**
 * Get theme option prefix.
 */
if (!function_exists('get_theme_option_prefix')) {
	function get_theme_option_prefix() {
		return hawp_theme()::$theme['option_prefix'];
	}
}

/**
 * Get theme option.
 */
if (!function_exists('get_theme_option')) {
	function get_theme_option($option_name) {
		$prefix = get_theme_option_prefix();
		return get_option($prefix . $option_name);
	}
}

/**
 * Debug theme option value.
 */
if (!function_exists('debug_theme_option')) {
	function debug_theme_option($option_name) {
		$prefix = get_theme_option_prefix();
		$full_option_name = $prefix . $option_name;
		$value = get_option($full_option_name);
		error_log("Debug - Option: {$full_option_name}, Value: " . print_r($value, true));
		return $value;
	}
}

/**
 * Gets the subpages of the current or a given page.
 *
 * @param int $post_id The post to check for children.
 * @param bool $all Whether or not to find all of them or just the first level.
 */
if (!function_exists('get_child_pages')) {
	function get_child_pages($post_id = null, $all = false) {
		if (! $post_id) {
			$post_id = get_global_post_ID();
		}

		$args = ($all) ? ['child_of' => $post_id] : ['parent' => $post_id];

		return get_pages($args);
	}
}

/**
 * Get the url of the current page or post.
 */
if (!function_exists('get_current_url')) {
	function get_current_url() {
		return esc_url(trailingslashit(home_url(add_query_arg($_GET, $GLOBALS['wp']->request))));
	}
}

/**
 * Get a random post.
 *
 * @param string $type The post type to query.
 */
if (!function_exists('get_random_post')) {
	function get_random_post($type = 'post') {
		$count = intval(wp_count_posts($type)->publish);
		$rand  = rand(0, $count);

		$posts = get_posts([
			'post_type' => $type,
			'numberposts' => 1,
			'offset' => max(0, $rand - 1),
		]);

		return !empty($posts) ? $posts[0] : null;
	}
}

/**
 * Retrieves and formats the edit post link for post.
 *
 * @param string $id The post to edit.
 */
if (!function_exists('insert_edit_post_link')) {
	function insert_edit_post_link($id) {
		$result = '';

		if (is_user_logged_in() && current_user_can('edit_posts')) {
			$result = '<a href="'.get_edit_post_link($id).'" target="_blank" class="user-edit-post-link" area-label="Edit this post" title="Edit this post" rel="nofollow"><svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="icon"><path fill="currentColor" d="M402.3 344.9l32-32c5-5 13.7-1.5 13.7 5.7V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h273.5c7.1 0 10.7 8.6 5.7 13.7l-32 32c-1.5 1.5-3.5 2.3-5.7 2.3H48v352h352V350.5c0-2.1.8-4.1 2.3-5.6zm156.6-201.8L296.3 405.7l-90.4 10c-26.2 2.9-48.5-19.2-45.6-45.6l10-90.4L432.9 17.1c22.9-22.9 59.9-22.9 82.7 0l43.2 43.2c22.9 22.9 22.9 60 .1 82.8zM460.1 174L402 115.9 216.2 301.8l-7.3 65.3 65.3-7.3L460.1 174zm64.8-79.7l-43.2-43.2c-4.1-4.1-10.8-4.1-14.8 0L436 82l58.1 58.1 30.9-30.9c4-4.2 4-10.8-.1-14.9z"></path></svg></a>';
		}

		return $result;
	}
}

/**
 * Get a multisite subsite slug from its url.
 *
 * @param variable $url The url
 */
if (!function_exists('get_subsite_slug_from_url')) {
	function get_subsite_slug_from_url($url) {
		if (is_multisite() && get_theme_option('separate_multisite_acf_json')) {
			if (defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL === true) {
				$parsed_url = parse_url($url);
				$host_parts = explode('.', $parsed_url['host']);
				return $host_parts[0];
			} else {
				$path = trim(parse_url($url, PHP_URL_PATH), '/');
				return $path !== '' ? $path : 'root';
			}
		} else {
			return '';
		}
	}
}

/**
 * Automatically registers ACF blocks by looping through a directory.
 *
 * @param string $blocks_dir The directory containing block folders
 */
if (!function_exists('auto_register_theme_blocks')) {
	function auto_register_theme_blocks($blocks_dir) {
		if (empty($blocks_dir) || !is_dir($blocks_dir)) {
			trigger_error('The provided $blocks_dir is not a valid directory path.', E_USER_WARNING);
			return;
		}

		if (is_dir($blocks_dir) && function_exists('register_block_type')) {
			$block_directories = glob($blocks_dir . '*', GLOB_ONLYDIR);

			foreach ($block_directories as $block_directory) {
				$block_name = basename($block_directory);
				register_block_type("{$blocks_dir}{$block_name}/");
			}
		}
	}
}

/**
 * Register and enqueue scripts and styles with array
 *
 * @deprecated 6.5.11 Use WordPress core functions wp_register_style(), wp_enqueue_style(), wp_register_script(), and wp_enqueue_script() instead.
 * @param array $array The scripts and styles array
 */
if (!function_exists('add_styles_and_scripts')) {
	function add_styles_and_scripts($array=[]) {
		// Trigger deprecation notice
		_deprecated_function(
			__FUNCTION__,
			'6.5.11',
			'WordPress core functions wp_register_style(), wp_enqueue_style(), wp_register_script(), and wp_enqueue_script()'
		);

		foreach($array as $data) {
			// Set enable to true by default
			$enable = isset($data['enable']) ? $data['enable'] : true;

			// Loop through styles
			if (isset($data['styles'])) {
				foreach ($data['styles'] as $style) {
					$args = [
						'handle' => $style[0],
						'src' => $style[1],
						'deps' => isset($style[2]) ? $style[2] : [],
						'ver' => isset($style[3]) ? $style[3] : false,
						'media' => isset($style[4]) ? $style[4] : 'all',
					];
					wp_register_style($args['handle'], $args['src'], $args['deps'], $args['ver'], $args['media']);

					// If enable is true, enqueue the style
					if ($enable === true) {
						wp_enqueue_style($args['handle']);
					}
				}
			}
			// Loop through scripts
			if (isset($data['scripts'])) {
				foreach ($data['scripts'] as $script) {
					$args = [
						'handle' => $script[0],
						'src' => $script[1],
						'deps' => isset($script[2]) ? $script[2] : [],
						'ver' => isset($script[3]) ? $script[3] : false,
						'in_footer' => isset($script[4]) ? $script[4] : true,
					];
					wp_register_script($args['handle'], $args['src'], $args['deps'], $args['ver'], $args['in_footer']);

					// If enable is true, enqueue the script
					if ($enable === true) {
						wp_enqueue_script($args['handle']);
					}
				}
			}
		}
	}
}

/**
 * Force SSL for sites that are set to SSL.
 */
if (!function_exists('force_ssl')) {
	function force_ssl() {
		if (!is_ssl() && stripos(home_url('/'), 'https://') === 0) {
			wp_redirect('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 301);
			exit();
		}
	}
}

/**
 * Add new post rewrite rule
 */
if (!function_exists('create_new_post_url_querystring')) {
	function create_new_post_url_querystring() {
		add_rewrite_rule(
			'blog/([^/]*)$',
			'index.php?name=$matches[1]',
			'top'
		);

		add_rewrite_tag('%blog%','([^/]*)');
	}
}

/**
 * Modify post link
 * This will print /blog/post-name instead of /post-name
 */
if (!function_exists('append_post_query_string')) {
	function append_post_query_string($url, $post, $leavename) {
		if ($post->post_type !== 'post') {
			return $url;
		}

		if (strpos($url, '%postname%') !== false) {
			$slug = '%postname%';
		} elseif ($post->post_name) {
			$slug = $post->post_name;
		} else {
			$slug = sanitize_title($post->post_title);
		}

		return home_url(user_trailingslashit('blog/'. $slug));
	}
}

/**
 * Redirect all posts to new url
 * If you get error 'Too many redirects' or 'Redirect loop', then delete everything below
 */
if (!function_exists('redirect_old_post_urls')) {
	function redirect_old_post_urls() {
		if (is_singular('post')) {
			global $post;

			if (strpos($_SERVER['REQUEST_URI'], '/blog/') === false) {
				wp_redirect(home_url(user_trailingslashit("blog/$post->post_name")), 301);
				exit();
			}
		}
	}
}
