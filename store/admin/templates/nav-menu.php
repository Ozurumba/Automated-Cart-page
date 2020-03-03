<?php if (!defined('PATH')) { exit; } ?>

    <nav class="pushy pushy-left">
      <div class="pushy-content">
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <?php
        // Left slider menu..
        if (!empty($slidePanelLeftMenu)) {
          $defMenuPanel = DEF_ADM_OPEN_MENU_PANEL;
          if (isset($_SESSION[mc_encrypt('adm_menu_panel' . SECRET_KEY)])) {
            $defMenuPanel = $_SESSION[mc_encrypt('adm_menu_panel' . SECRET_KEY)];
          }
          foreach (array_keys($slidePanelLeftMenu) AS $smk) {
            if (!empty($slidePanelLeftMenu[$smk]['links'])) {
              ?>
              <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading<?php echo $smk; ?>">
                  <h4 class="panel-title">
                    <a<?php echo ($smk != $defMenuPanel ? ' class="collapsed" ' : ' '); ?>role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $smk; ?>" aria-expanded="<?php echo ($smk == $defMenuPanel ? 'true' : 'false'); ?>" onclick="mswPanel('<?php echo $smk; ?>')" aria-controls="collapse<?php echo $smk; ?>" title="<?php echo mc_safeHTML($slidePanelLeftMenu[$smk][0]); ?>">
                      <i class="fa fa-<?php echo $slidePanelLeftMenu[$smk][1]; ?> fa-fw"></i> <?php echo $slidePanelLeftMenu[$smk][0]; ?>
                    </a>
                  </h4>
                </div>
                <div id="collapse<?php echo $smk; ?>" class="panel-collapse collapse<?php echo ($smk == $defMenuPanel ? ' in' : ''); ?>" role="tabpanel" aria-labelledby="heading<?php echo $smk; ?>">
                  <div class="panel-body linkbodyarea">
                  <?php
                  if (!empty($slidePanelLeftMenu[$smk]['links'])) {
                    for ($i=0; $i<count($slidePanelLeftMenu[$smk]['links']); $i++) {
                    ?>
                    <div><a href="<?php echo $slidePanelLeftMenu[$smk]['links'][$i]['url']; ?>" title="<?php echo mc_safeHTML($slidePanelLeftMenu[$smk]['links'][$i]['name']); ?>"><?php echo $slidePanelLeftMenu[$smk]['links'][$i]['name']; ?></a></div>
                    <?php
                    }
                  }
                  ?>
                  </div>
                </div>
              </div>
              <?php
            }
          }
        }
        ?>
        </div>
      </div>
		</nav>

    <div class="site-overlay"></div>