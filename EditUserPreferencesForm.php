<?php

class EditUserPreferencesForm extends PreferencesForm {
	public function getExtraSuccessRedirectParameters() {
		return [ 'username' => $this->getModifiedUser()->getName() ];
	}

	function getButtons() {
		$attrs = [ 'id' => 'mw-prefs-restoreprefs' ];

		$html = HTMLForm::getButtons();

		$url = SpecialPage::getTitleFor( 'EditUser' )->getFullURL(
			[ 'reset' => 1, 'username' => $this->getModifiedUser()->getName() ]
		);

		$html .= "\n" . Xml::element( 'a', [ 'href' => $url ],
			$this->msg( 'restoreprefs' )->escaped(),
				Html::buttonAttributes( $attrs, [ 'mw-ui-quiet' ] ) );

		$html = Xml::tags( 'div', [ 'class' => 'mw-prefs-buttons' ], $html );

		return $html;
	}
}
