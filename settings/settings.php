<hr>

<h2><?=__('Admin Menu Scroll', BT_ADMIN_MENU_TEXT_DOMAIN)?></h2>
<form action="options.php" method="post">
    <?php
    settings_fields(BT_ADMIN_MENU_OPTIONS_KEY);
    do_settings_sections(BT_ADMIN_MENU_SLUG);
    submit_button();
    ?>
</form>

<hr>
