<?php
defined('ABSPATH') || exit;

?>

<div id="satollo-menu">
    <div id="satollo-menu-title">Injections</div>
    <div id="satollo-menu-nav">
        <ul>
            <li><a href="?page=hefo&subpage=settings">Settings</a></li>
            <li><a href="?page=hefo&subpage=more">More...</a></li>
        </ul>
    </div>

    <div></div>
</div>
<script>
    jQuery(function () {
        jQuery('#satollo-menu-nav a').each(function () {
            if (location.href.indexOf(this.href) >= 0) {
                jQuery(this).addClass('satollo-active');
            }
        });
    });
</script>
