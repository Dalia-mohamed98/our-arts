<h1>AdRoll for WooCommerce</h1>

<h2>Configuring your store. Please wait...</h2>

<form id="adroll_settings" action="options.php" method="POST">
	<?php settings_fields('AdRoll') ?>
	<?php do_settings_sections('wp_adroll') ?>
</form>


<script type="text/javascript">
    window.setTimeout(function() {document.getElementById("adroll_settings").submit();}, 2000);
</script>
