<?php

class EditUserPreferencesForm extends PreferencesForm {
	public function getExtraSuccessRedirectParameters() {
		return array( 'username' => $this->getModifiedUser()->getName() );
	}

	function getButtons() {
		$attrs = array( 'id' => 'mw-prefs-restoreprefs' );

		$html = HTMLForm::getButtons();

		$url = SpecialPage::getTitleFor( 'EditUser' )->getFullURL(
			array( 'reset' => 1, 'username' => $this->getModifiedUser()->getName() )
		);

		$html .= "\n" . Xml::element( 'a', array( 'href'=> $url ),
			$this->msg( 'restoreprefs' )->escaped(),
				Html::buttonAttributes( $attrs, array( 'mw-ui-quiet' ) ) );

		$html = Xml::tags( 'div', array( 'class' => 'mw-prefs-buttons' ), $html );

		return $html;
	}
}
