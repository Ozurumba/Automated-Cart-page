<?php if (!defined('PATH') || !isset($msTopMenu) || empty($slidePanelLeftMenu)) { exit; } ?>
          </div>
        </div>
      </div>

      <footer>
      <?php
      // Please don`t remove the footer unless you have purchased a licence..
      // https://www.maiancart.com/purchase.html
      if (LICENCE_VER == 'unlocked' && $SETTINGS->adminFooter) {
        echo mc_cleanData($SETTINGS->adminFooter);
      } else {
      ?>
      Powered by: <a href="https://www.maiancart.com" onclick="window.open(this);return false" title="Maian Cart">Maian Cart</a><br>
      <a href="https://www.maianscriptworld.co.uk" title="Maian Script World" onclick="window.open(this);return false">&copy; 2006 - <?php echo date('Y'); ?> Maian Script World</a>
      <?php
      }
      ?>
      </footer>

    </div>

    <script src="templates/js/bootstrap.js"></script>
    <script src="templates/js/plugins/bootstrap.dialog.js"></script>

    <script src="templates/js/mnc-admin.js"></script>
    <script src="templates/js/mnc.js"></script>

    <?php
    if (defined('LOAD_DATE_PICKERS')) {
      include(PATH . 'templates/js-loader/date-picker.php');
    }

    if (isset($loadElFinder) && is_dir($SETTINGS->globalDownloadPath . '/' . $SETTINGS->downloadFolder) &&
      is_writeable($SETTINGS->globalDownloadPath . '/' . $SETTINGS->downloadFolder)) {
    ?>
    <script src="templates/js/plugins/jquery.elfinder.min.js"></script>
    <?php
    $elFnLng = (ELF_LOCALE ? strtolower(ELF_LOCALE) : 'en');
    if ($elFnLng != 'en' && file_exists(PATH . 'templates/js/i18n/elfinder.' . $elFnLng . '.js')) {
    ?>
    <script src="templates/js/i18n/elfinder.<?php echo $elFnLng; ?>.js"></script>
    <?php
    }
    ?>
    <script charset="utf-8">
    //<![CDATA[
    jQuery(document).ready(function() {
		  jQuery('#elfinder').elfinder({
			  url  : 'index.php?p=ajax-ops&op=elfinder',
				lang : '<?php echo $elFnLng; ?>'
			});
		});
    //]]>
    </script>
    <?php
    }

    if (in_array($cmd, array('stats','sales-trends','coupon-report','main','gift-report'))) {
    ?>
    <script src="templates/js/jqplot/jquery.jqplot.min.js"></script>
    <script src="templates/js/jqplot/jqplot.lgAxisRenderer.min.js"></script>
    <script src="templates/js/jqplot/jqplot.canvasTextRenderer.min.js"></script>
    <script src="templates/js/jqplot/jqplot.canvasAxisLabelRenderer.min.js"></script>
    <script src="templates/js/jqplot/jqplot.canvasAxisTickRenderer.min.js"></script>
    <script src="templates/js/jqplot/jqplot.dateAxisRenderer.min.js"></script>
    <script src="templates/js/jqplot/jqplot.categoryAxisRenderer.min.js"></script>
    <script src="templates/js/jqplot/jqplot.barRenderer.min.js"></script>
    <script src="templates/js/jqplot/jqplot.highlighter.min.js"></script>
    <?php
    if (in_array($cmd, array('coupon-report','gift-report','stats'))) {
    ?>
    <script src="templates/js/jqplot/jqplot.barRenderer.js"></script>
    <script src="templates/js/jqplot/jqplot.pointLabels.js"></script>
    <?php
    }
    }

    if (defined('JS_LOADER')) {
      include(PATH . 'templates/js-loader/' . JS_LOADER);
    }

    if (isset($loadiBox)) {
    ?>
    <script src="templates/js/plugins/jquery.ibox.js"></script>
    <?php
    }

    if (isset($textareaFullScr)) {
    ?>
    <script src="templates/js/plugins/jquery.textareafullscreen.js"></script>
    <script>
    //<![CDATA[
    jQuery(document).ready(function() {
      jQuery('textarea').textareafullscreen({
        overlay: true,
        maxWidth: '80%',
        maxHeight: '80%'
      });
    });
    //]]>
    </script>
    <?php
    }

    // Tooltip
    ?>
    <script>
    //<![CDATA[
    jQuery(document).ready(function() {
      jQuery('[data-toggle="tooltip"]').tooltip({
        container : 'body',
        trigger   : 'click',
        template  : '<div class="tooltip tooltipformat" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
      });
    });
    //]]>
    </script>

    <?php
    // Action spinner, DO NOT REMOVE
    ?>
    <div class="overlaySpinner" style="display:none"></div>
    <?php
    // Off canvas menu..
    include(PATH . 'templates/nav-menu.php');
    ?>
    <script src="templates/js/plugins/jquery.pushy.js"></script>

  </body>
</html>