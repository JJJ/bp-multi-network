<?php

/**
 * Plugin Name: BP Multi Network
 * Plugin URI:  https://wordpress.org/plugins/bp-multi-network/
 * Description: Unique BuddyPress networks in your WordPress multi-network installation
 * Version:     0.2.0
 * Author:      The BuddyPress Community
 * Author URI:  https://buddypress.org
*/

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The main BuddyPress multi-network class
 *
 * This class adds filters to three places in BuddyPress to intercept and modify
 * database table prefixes based on the current network.
 */
class BP_Multi_Network {

	/**
	 * Array of BuddyPress user-meta keys that should not be shared across all
	 * networks. Rather, these keys are per-network, and need to be
	 * appropriately namespaced to avoid collisions.
	 *
	 * @var array
	 */
	private $user_meta_keys = array(
		'last_activity'                             => false,
		'bp_new_mentions'                           => false,
		'bp_new_mention_count'                      => false,
		'bp_favorite_activities'                    => false,
		'bp_latest_update'                          => false,
		'total_friend_count'                        => false,
		'total_group_count'                         => false,
		'notification_activity_new_mention'         => false,
		'notification_activity_new_reply'           => false,
		'notification_groups_group_updated'         => false,
		'notification_groups_membership_request'    => false,
		'notification_membership_request_completed' => false,
		'notification_groups_admin_promotion'       => false,
		'notification_groups_invite'                => false,
		'notification_messages_new_message'         => false,
		'notification_messages_new_notice'          => false,
		'closed_notices'                            => false,
		'profile_last_updated'                      => false
	);

	/** Filters ***************************************************************/

	/**
	 * Attach filters to what are usually considered "global" values, and modify
	 * them based on the currently loaded WordPress network of sites.
	 *
	 * - Global cache key
	 * - Table prefix
	 * - User meta key
	 */
	public function __construct() {
		add_filter( 'bp_core_get_table_prefix', array( $this, 'filter_table_prefix'  ) );
		add_filter( 'bp_get_user_meta_key',     array( $this, 'filter_user_meta_key' ) );
	}

	/**
	 * Filter the DB table prefix and maybe add a network ID
	 *
	 * @param  string $prefix
	 * @return string
	 */
	public function filter_table_prefix( $prefix = '' ) {

		// Override prefix if not main network and there is a prefix match
		if ( ! self::is_main_network() && ( true === self::is_base_db_prefix( $prefix ) ) ) {
			$prefix = self::get_network_db_prefix();
		}

		// Use this network's prefix
		return $prefix;
	}

	/**
	 * Filter the appropriate BuddyPress user-meta keys and prefix them with the
	 * ID of the network they belong to.
	 *
	 * @param  string $key
	 * @return string
	 */
	public function filter_user_meta_key( $key = '' ) {

		// Bail if key is not BuddyPress user meta
		if ( ! isset( $this->user_meta_keys[ $key ] ) ) {
			return $key;
		}

		// Bail if on the main network
		if ( self::is_main_network() ) {
			return $key;
		}

		// Set the user meta key to the new prefix
		if ( false === $this->user_meta_keys[ $key ] ) {
			$this->user_meta_keys[ $key ] = self::get_network_db_prefix() . $key;
		}

		// Return the modified user meta key
		return $this->user_meta_keys[ $key ];
	}

	/** Network ***************************************************************/

	/**
	 * Whether or not the current DB query is from the main network.
	 *
	 * The main network is typically ID 1 (and does not have a modified prefix)
	 * but we also need to check for the PRIMARY_NETWORK_ID constant introduced
	 * in WordPress 3.7 for more sophisticated installations.
	 *
	 * @return boolean
	 */
	private static function is_main_network() {
		return (bool) ( self::get_network_id() === (int) self::get_wpdb()->siteid );
	}

	/** DB ********************************************************************/

	/**
	 * Compare a given DB table prefix with the base DB prefix.
	 *
	 * @param  string $prefix
	 * @return string
	 */
	private static function is_base_db_prefix( $prefix = '' ) {
		return (bool) ( self::get_wpdb()->base_prefix === $prefix );
	}

	/**
	 * Return the DB table prefix for the current network
	 *
	 * Note that we're using the prefix for the root-blog, and not the network
	 * ID itself. This is because BuddyPress stores much of its data in the
	 * root-blog options table VS the sitemeta table.
	 *
	 * @return string
	 */
	private static function get_network_db_prefix() {
		return self::get_wpdb()->get_blog_prefix( self::get_site_id() );
	}

	/** Helpers ***************************************************************/

	/**
	 * Get the root blog ID for the current network
	 *
	 * @global int $blog_id
	 * @return int
	 */
	private static function get_site_id() {
		return (int) function_exists( 'get_current_site' )
			? get_current_site()->blog_id
			: $GLOBALS['blog_id'];
	}

	/**
	 * Use primary network ID if defined
	 *
	 * @return int
	 */
	private static function get_network_id() {
		return (int) defined( 'PRIMARY_NETWORK_ID' )
			? PRIMARY_NETWORK_ID
			: 1;
	}

	/**
	 * Return the global $wpdb object
	 *
	 * @return object
	 */
	private static function get_wpdb() {
		return isset( $GLOBALS['wpdb'] )
			? $GLOBALS['wpdb']
			: false;
	}
}
new BP_Multi_Network();
