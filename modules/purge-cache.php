<?php
/**
 * Plugin name: WP-palvelu Purge Cache
 * Description: Purges the WP-palvelu cache
 * Version: 1.0
 */

/**
 * Add a purge button in the WP Admin Bar
 */
add_action( 'admin_bar_menu', '_wpp_purge_button', 999 );
function _wpp_purge_button( $admin_bar ) {

  // check permissions
  if ( !current_user_can( 'manage_options' ) ) {
    return;
  }

  $purge_url = add_query_arg( 'wpp_purge_cache', '1' );
  $title = 
  $admin_bar->add_menu( array(
    'id' => 'nginx-helper-purge-all',
    'title' => '<span class="ab-icon"></span><span title="'.
    sprintf(__('WP-Palvelu uses proxy to deliver lightning fast responses to your visitors. Cached pages will be refreshed every %s. This button is used for clearing all cached pages from the frontend proxy immediately.','wpp'),"15 minutes").
    '" class="ab-label">'.__('Purge Cache','wpp')."</span",
    'href' => wp_nonce_url( $purge_url, '_wpp_purge', '_wpp_nonce' ),
  ));
}

/**
 * Add Refresh Icon css for Purge Cache button into admin head
 */
add_action( 'admin_head', '_wpp_purge_admin_style' );
function _wpp_purge_admin_style() { ?>
<style type="text/css" media="screen">
  #wpadminbar #wp-admin-bar-nginx-helper-purge-all .ab-icon:before {
    content: "\f463";
    top: 3px;
  }  
</style>
<?php }


/**
 * Purge the cache via REQUEST parameters
 */
add_action( 'admin_init', '_maybe_wpp_purge_cache' );
function  _maybe_wpp_purge_cache() {

  // check permissions
  if ( !current_user_can( 'manage_options' ) ) {
    return;
  }

  if( isset($_REQUEST['wpp_purge_cache']) ) {

    // check nonce
    if (!isset($_GET['_wpp_nonce']) || !wp_verify_nonce($_GET['_wpp_nonce'], '_wpp_purge')) {
      return;
    }

    // purge the cache
    $response = _wpp_purge_cache();
    error_log( "NOTICE: Cache flush initiated from admin: \n" . $response );

    // redirect to the original siteurl with notification
    $redirect_url = remove_query_arg( array( 'wpp_purge_cache', '_wpp_nonce' ) );
    $redirect_url = add_query_arg( 'wpp_purge_success', 1, $redirect_url );
    wp_redirect($redirect_url);

    die();
  }
}

/**
 * Displays the cache purged notification
 */
add_action( 'admin_notices', '_wpp_purge_notification' );
function _wpp_purge_notification() {

  // check permissions
  if ( !current_user_can( 'manage_options' ) ) {
    return;
  }

  // check to see if we should show notification
  if(!isset($_REQUEST['wpp_purge_success'])) {
    return;
  }

  ?>
  <div class="notice updated is-dismissible">
      <p><strong><?php _e( 'Success:' ); ?></strong> <?php _e( 'The cache was flushed' ); ?> <button type="button" class="notice-dismiss"></button></p>
  </div>
  <?php
}

/**
 * Purges the cache
 */
function _wpp_purge_cache() {

  // send a purge request to the downstream server
  $ch = curl_init( get_site_url( null, '/purge/' ) );
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PURGE');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $return = curl_exec($ch);

  return $return;
}

