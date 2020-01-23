<?php

class gdprPrivacyDataModuleFrontController extends ModuleFrontController {
    public function initContent() {
        parent::initContent();
        $this->setTemplate('privacydata.tpl');
    }
}
