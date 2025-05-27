<?php

namespace Automattic\WooCommerce\Caching;

/**
 * Previosly we implemented cache namespaces using a prefix for the cache key. But since Redis is guranteed now, we can directly use groups.
 * Note this assumes `fast_build_key` method in `wp_object_cache` is available, which is the case for Redis Object Cache plugin.
 */
trait CacheNameSpaceTrait {

	/**
	 * Get prefix for use with wp_cache_set. Allows all cache in a group to be invalidated at once.
	 *
	 * @param  string $group Group of cache to get.
	 * @return string Prefix.
	 */
	public static function get_cache_prefix( $group ) {
		return $group;
	}

	/**
	 * Increment group cache prefix (invalidates cache).
	 *
	 * @param string $group Group of cache to clear.
	 */
	public static function incr_cache_prefix( $group ) {
		wc_deprecated_function( 'WC_Cache_Helper::incr_cache_prefix', '3.9.0', 'WC_Cache_Helper::invalidate_cache_group' );
		self::invalidate_cache_group( $group );
	}

	/**
	 * Invalidate cache group.
	 *
	 * @param string $group Group of cache to clear.
	 * @since 3.9.0
	 */
	public static function invalidate_cache_group( $group ) {
		return wp_cache_flush_group( $group );
	}

	/**
	 * Helper method to get prefixed key.
	 *
	 * @param  string $key   Key to prefix.
	 * @param  string $group Group of cache to get.
	 *
	 * @return string Prefixed key.
	 */
	public static function get_prefixed_key( $key, $group ) {
		global $wp_object_cache;
		return $wp_object_cache->fast_build_key( $key, $group );
	}
}
