<?php

final class PhabricatorDateTimeSettingsPanel
  extends PhabricatorEditEngineSettingsPanel {

  const PANELKEY = 'datetime';

  public function getPanelName() {
    return pht('Date and Time');
  }

  public function getPanelGroupKey() {
    return PhabricatorSettingsAccountPanelGroup::PANELGROUPKEY;
  }

  public function isEditableByAdministrators() {
    return true;
  }

}
