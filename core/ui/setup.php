<?php $pkgDir = str_replace( ROOT_DIR, '', BP_PACKAGE_DIR ); ?>
<!doctype html>
<html>
<head>

    <title>Get Started with Boilerplate</title>
    <link rel="stylesheet" href="<?= $pkgDir; ?>/core/ui/css/boilerplate.css">

</head>
<body>

    <header class="header">
        <img src="<?= $pkgDir; ?>/core/ui/images/logo.png" alt="Boilerplate Logo">
    </header>

    <?php if ( !file_exists( ROOT_DIR . '/_includes/vendor/autoload.php' ) ): ?>
    <div class="container">
        <h2 class="pre-title">Install Dependencies</h2>
        <h1>Before you get started</h1>

        <p>Before you start building, make sure you install Boilerplate's dependencies via Composer. You can either start the built in PHP server by running <code>php serve</code> and it will attempt installation for you or you can run <code>composer install</code> from your terminal.</p>
    </div>
    <?php endif; ?>

    <div class="container">
        <h2 class="pre-title">Customise Boilerplate</h2>
        <h1>Add Plugins</h1>

        <div id="plugin-list" class="trace"></div>
    </div>

    <div class="container">
        <h4>Why am I seeing this screen?</h4>
        <p>You've got <code>admin_mode</code> set to <code>true</code>. Change it to <code>false</code> in your config file if you're done here.</p>
    </div>

<script>
window.pkgDir = "<?= $pkgDir; ?>";
</script>
<script src="<?= $pkgDir; ?>/core/ui/js/reef.js"></script>
<script src="<?= $pkgDir; ?>/core/ui/js/plugin-list.js"></script>
</body>
</html>
<?php exit; // Stop anymore output ?>
