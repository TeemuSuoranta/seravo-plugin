<div class="wrap">

<h1>Cruft files</h1>

<h2>Find unnecessary files in the filesystem</h2>
<p>
<div id="cruftfiles_status_loading"><img src="/wp-admin/images/spinner.gif"></div>
<div id="cruftfiles_status"></div>
</p>

<script>
// Generic ajax report loader function
function seravo_load_report(section) {
  jQuery.post(
    ajaxurl,
    { 'action': 'seravo_cruftfiles',
      'section': section },
    function(rawData) {
      if (rawData.length == 0) {
        jQuery('#' + section).html('No data returned for section.');
      }

      jQuery('#' + section + '_loading').fadeOut();
      console.log(rawData);
      var data = JSON.parse(rawData);
      jQuery.each( data, function( i, file){

        jQuery( '#cruftfiles_status' ).append("<p>" + file + "</p>");
      });
      jQuery( '#cruftfiles_status_loading img' ).fadeOut();
    }
  ).fail(function() {
    jQuery('#' + section + '_loading').html('Failed to load. Please try again.');
  });
}

// Load on page load
seravo_load_report('cruftfiles_status');

</script>

</div>
