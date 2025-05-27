<?php

namespace Automattic\WooCommerce\Internal\Redis;

defined( 'ABSPATH' ) || exit;

/**
 * Redis connection and management for Starshop using Predis.
 */
class RedisManager {
	/**
	 * Redis connection instance.
	 *
	 * @var \Predis\Client|null
	 */
	protected $client = null;

	/**
	 * Initialize Redis connection.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the Redis connection.
	 */
	public function init() {
		if ( ! $this->is_available() ) {
			return;
		}

		try {
			$this->connect();
		} catch ( \Exception $e ) {
			// Log connection error
			if ( class_exists( 'WC_Logger' ) ) {
				$logger = new \WC_Logger();
				$logger->error( 'Redis connection error: ' . $e->getMessage(), array( 'source' => 'redis-connection' ) );
			}
		}
	}

	/**
	 * Check if Predis library is available.
	 *
	 * @return bool
	 */
	public function is_available() {
		return class_exists( 'Predis\Client' );
	}

	/**
	 * Connect to Redis server.
	 */
	protected function connect() {
		$host    = defined( 'WC_REDIS_HOST' ) ? WC_REDIS_HOST : '127.0.0.1';
		$port    = defined( 'WC_REDIS_PORT' ) ? WC_REDIS_PORT : 6379;
		$timeout = defined( 'WC_REDIS_TIMEOUT' ) ? WC_REDIS_TIMEOUT : 1;
		$auth    = defined( 'WC_REDIS_AUTH' ) ? WC_REDIS_AUTH : '';

		$parameters = array(
			'host'    => $host,
			'port'    => $port,
			'timeout' => $timeout,
		);

		if ( ! empty( $auth ) ) {
			$parameters['password'] = $auth;
		}

		try {
			$this->client = new \Predis\Client( $parameters );
		} catch ( \Predis\Connection\ConnectionException $e ) {
			throw new \Exception( 'Could not connect to Redis server: ' . $e->getMessage() );
		} catch ( \Predis\ClientException $e ) {
			throw new \Exception( 'Redis client error: ' . $e->getMessage() );
		}

		return true;
	}

	/**
	 * Get the Redis instance.
	 *
	 * @return \Predis\Client|null
	 */
	public function get_client() {
		return $this->client;
	}
}
