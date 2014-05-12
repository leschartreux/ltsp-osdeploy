<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
        <title><?php echo $title; ?></title>
        <meta http-equiv="pragma" content="no-cache" />
        <meta name="author" lang="fr" content="DOSICALU" />
        <meta name="publisher" content="DOSICALU" />
        <meta name="copyright" content="JeDDLaJ 2005-2011" />
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <meta name="robots" content="index,follow" />
        <link rel="shortcut icon" type="images/x-icon" href="<?php echo $favicon; ?>" />
        <?php echo $style; ?>
    </head>
    <body>
        <div id="document" >
            <div id="tete" >
     <h1><?php echo $pageTitle; ?></h1>
            </div>
            <div class="clear"><hr /></div>


            <div id="corps">

                <p id="notification"><?php echo $notification; ?></p>

                <?php echo $content; ?>

            </div>
            <div id="pied">
                &copy; 2010-2011 DOSICALU
            </div>

        </div>
        <?php echo $script; ?>
    </body>
</html>