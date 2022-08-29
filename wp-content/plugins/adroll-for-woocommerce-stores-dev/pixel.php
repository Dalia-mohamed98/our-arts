<div id="woocommerce-adroll-pixel" style="display: none;">
<?php if($this->is_configured()): //dummy configs for optimizers
?>
adroll_adv_id = "<?php echo get_option('adroll_adv_eid') ?>";
adroll_pix_id = "<?php echo get_option('adroll_pixel_eid') ?>";
adroll_version = "2.0";
<?php
    $global_vars = $this->get_global_vars();
    foreach($global_vars as $name => $value) {
        echo "\t$name = " . json_encode($value) . ";\n";
    }
 ?>
<?php endif ?>
</div>
<script type="text/javascript" data-adroll="woocommerce-adroll-pixel">
<?php if($this->is_configured()): ?>
<?php
    //this allows for some customization, like including extra tracking data etc
    do_action('adroll_before_pixel', $this->get_current_page(), $this->get_global_vars());
?>
    adroll_adv_id = "<?php echo get_option('adroll_adv_eid') ?>";
    adroll_pix_id = "<?php echo get_option('adroll_pixel_eid') ?>";
    adroll_version = "2.0";
<?php foreach($global_vars as $name => $value) {
    echo "\t$name = " . json_encode($value) . ";\n";
}?>

    (function(w,d,e,o,a){
        w.__adroll_loaded=true;
        w.adroll=w.adroll||[];
        w.adroll.f=['setProperties','identify','track'];
        var roundtripUrl="https://s.adroll.com/j/ADV/roundtrip.js".replace("ADV", adroll_adv_id);
        for(a=0;a<w.adroll.f.length;a++){
            w.adroll[w.adroll.f[a]]=w.adroll[w.adroll.f[a]]||(function(n){return function(){w.adroll.push([n,arguments])}})(w.adroll.f[a])};e=d.createElement('script');o=d.getElementsByTagName('script')[0];e.async=1;e.src=roundtripUrl;o.parentNode.insertBefore(e, o);})(window,document);
<?php
    //this allows for some customization, like including extra tracking data etc
    do_action('adroll_after_pixel', $this->get_current_page(), $this->get_global_vars());
?>
<?php else: ?>
        var adrollPixelGuardVar= "adroll-pixel-guard";
<?php endif ?>
</script>
