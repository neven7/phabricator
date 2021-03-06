<?php

final class PhabricatorSettingsEditEngine
  extends PhabricatorEditEngine {

  const ENGINECONST = 'settings.settings';

  private $isSelfEdit;
  private $profileURI;

  public function setIsSelfEdit($is_self_edit) {
    $this->isSelfEdit = $is_self_edit;
    return $this;
  }

  public function getIsSelfEdit() {
    return $this->isSelfEdit;
  }

  public function setProfileURI($profile_uri) {
    $this->profileURI = $profile_uri;
    return $this;
  }

  public function getProfileURI() {
    return $this->profileURI;
  }

  public function isEngineConfigurable() {
    return false;
  }

  public function getEngineName() {
    return pht('Settings');
  }

  public function getSummaryHeader() {
    return pht('Edit Settings Configurations');
  }

  public function getSummaryText() {
    return pht('This engine is used to edit settings.');
  }

  public function getEngineApplicationClass() {
    return 'PhabricatorSettingsApplication';
  }

  protected function newEditableObject() {
    return new PhabricatorUserPreferences();
  }

  protected function newObjectQuery() {
    return new PhabricatorUserPreferencesQuery();
  }

  protected function getObjectCreateTitleText($object) {
    return pht('Create Settings');
  }

  protected function getObjectCreateButtonText($object) {
    return pht('Create Settings');
  }

  protected function getObjectEditTitleText($object) {
    return pht('Edit Settings');
  }

  protected function getObjectEditShortText($object) {
    return pht('Edit Settings');
  }

  protected function getObjectCreateShortText() {
    return pht('Create Settings');
  }

  protected function getObjectName() {
    $page = $this->getSelectedPage();

    if ($page) {
      return $page->getLabel();
    }

    return pht('Settings');
  }

  protected function getEditorURI() {
    return '/settings/edit/';
  }

  protected function getObjectCreateCancelURI($object) {
    return '/settings/';
  }

  protected function getObjectViewURI($object) {
    // TODO: This isn't correct...
    return '/settings/user/'.$this->getViewer()->getUsername().'/';
  }

  protected function getCreateNewObjectPolicy() {
    return PhabricatorPolicies::POLICY_ADMIN;
  }

  public function getEffectiveObjectEditCancelURI($object) {
    if ($this->getIsSelfEdit()) {
      return null;
    }

    if ($this->getProfileURI()) {
      return $this->getProfileURI();
    }

    return parent::getEffectiveObjectEditCancelURI($object);
  }

  protected function newPages($object) {
    $viewer = $this->getViewer();
    $user = $object->getUser();

    $panels = PhabricatorSettingsPanel::getAllPanels();

    foreach ($panels as $key => $panel) {
      if (!($panel instanceof PhabricatorEditEngineSettingsPanel)) {
        unset($panels[$key]);
        continue;
      }

      $panel->setViewer($viewer);
      if ($user) {
        $panel->setUser($user);
      }
    }

    $pages = array();
    $uris = array();
    foreach ($panels as $key => $panel) {
      $uris[$key] = $panel->getPanelURI();

      $page = $panel->newEditEnginePage();
      if (!$page) {
        continue;
      }
      $pages[] = $page;
    }

    $more_pages = array(
      id(new PhabricatorEditPage())
        ->setKey('extra')
        ->setLabel(pht('Extra Settings'))
        ->setIsDefault(true),
    );

    foreach ($more_pages as $page) {
      $pages[] = $page;
    }

    return $pages;
  }

  protected function buildCustomEditFields($object) {
    $viewer = $this->getViewer();
    $settings = PhabricatorSetting::getAllEnabledSettings($viewer);

    foreach ($settings as $key => $setting) {
      $setting = clone $setting;
      $setting->setViewer($viewer);
      $settings[$key] = $setting;
    }

    $settings = msortv($settings, 'getSettingOrderVector');

    $fields = array();
    foreach ($settings as $setting) {
      foreach ($setting->newCustomEditFields($object) as $field) {
        $fields[] = $field;
      }
    }

    return $fields;
  }

}
