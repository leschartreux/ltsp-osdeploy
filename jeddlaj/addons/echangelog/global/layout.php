<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
        <title><?php echo $this->_title; ?></title>
        <meta http-equiv="pragma" content="no-cache" />
        <meta name="author" lang="fr" content="La Firme" />
        <meta name="publisher" content="La Firme" />
        <meta name="copyright" content="JeDDLaJ 2005-2010" />
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <meta name="robots" content="index,follow" />
        <link rel="shortcut icon" type="images/x-icon" href="data/images/favicon.ico" />
        <link rel="stylesheet" href="style/echangelog.css"  type="text/css" media="screen" />
        <?php echo $this->_style; ?>
    </head>
    <body>
        <div id="document" >
            <div id="tete" >
                <h1><a href="index.php?module=echangelog" title="Retour à l'accueil du module e-changelog">e-ChangeLog</a></h1>
            </div>
            <div class="clear"><hr /></div>


            <div id="corps">

                <?php echo $this->_message; ?>

                <?php echo $this->_content; ?>

            </div>
            <div id="pied">
                &copy; 2005-2011 DOSICALU
            </div>

        </div>
        <?php echo $this->_script; ?>
    </body>
</html>
