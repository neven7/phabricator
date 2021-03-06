<?php

final class PhabricatorAccountSettingsPanel
  extends PhabricatorEditEngineSettingsPanel {

  const PANELKEY = 'account';

  public function getPanelName() {
    return pht('Account');
  }

  public function getPanelGroupKey() {
    return PhabricatorSettingsAccountPanelGroup::PANELGROUPKEY;
  }

  public function isEditableByAdministrators() {
    return true;
  }

}
