<?php
/* Shamelessly copied and modified from /includes/specials/SpecialPreferences.php v1.27alpha */
/**
 * Implements Special:Preferences
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup SpecialPage
 */

/**
 * A special page that allows users to change their preferences
 *
 * @ingroup SpecialPage
 */
class EditUser extends SpecialPage {
	function __construct() {
		parent::__construct( 'EditUser', 'edituser' );
	}

	public function execute( $par ) {
		$user = $this->getUser();
		$out = $this->getOutput();

		if ( !$user->isAllowed( 'edituser' ) ) {
			throw new PermissionsError( 'edituser' );
		}

		$this->setHeaders();

		$request = $this->getRequest();
		$this->target = ( isset( $par ) ) ? $par : $request->getText( 'username', '' );
		if ( $this->target === '' ) {
			$out->addHtml( $this->makeSearchForm() );
			return;
		}
		$targetuser = User::NewFromName( $this->target );
		if ( $targetuser->getID() == 0 ) {
			$out->addWikiMsg( 'edituser-nouser', htmlspecialchars( $this->target ) );
			return;
		}
		$this->targetuser = $targetuser;
		# Allow editing self via this interface
		if ( $targetuser->isAllowed( 'edituser-exempt' ) && $targetuser->getName() != $user->getName() ) {
			$out->addWikiMsg( 'edituser-exempt', $targetuser->getName() );
			return;
		}

		$this->setHeaders();
		$this->outputHeader();
		$out->disallowUserJs(); # Prevent hijacked user scripts from sniffing passwords etc.

		$this->checkReadOnly();

		if ( $request->getCheck( 'reset' ) ) {
			$this->showResetForm();

			return;
		}

		$out->addModules( 'mediawiki.special.preferences' );
		$out->addModuleStyles( 'mediawiki.special.preferences.styles' );

		// $this->loadGlobals( $this->target );
		$out->addHtml( $this->makeSearchForm() . '<br />' );
		# End EditUser additions

		if ( $this->getRequest()->getCheck( 'success' ) ) {
			$out->wrapWikiMsg(
				Html::rawElement(
					'div',
					array(
						'class' => 'mw-preferences-messagebox successbox',
						'id' => 'mw-preferences-success'
					),
					Html::element( 'p', array(), '$1' )
				),
				'savedprefs'
			);
		}

		if ( $this->getRequest()->getCheck( 'eauth' ) ) {
			$out->wrapWikiMsg(
				Html::rawElement(
					'div',
					array(
						'class' => 'error',
						'style' => 'clear: both;'
					),
					Html::element( 'p', array(), '$1' )
				),
				'eauthentsent',
				$this->target
			);
		}

		$this->addHelpLink( 'Help:Preferences' );

		$htmlForm = Preferences::getFormObject( $targetuser, $this->getContext(),
			'EditUserPreferencesForm', array( 'password' ) );
		$htmlForm->setSubmitCallback( 'Preferences::tryUISubmit' );
		$htmlForm->addHiddenField( 'username', $this->target );

		$htmlForm->show();
	}

	private function showResetForm() {
		if ( !$this->getUser()->isAllowed( 'editmyoptions' ) ) {
			throw new PermissionsError( 'editmyoptions' );
		}

		if ( !$this->getUser()->isAllowed( 'edituser' ) ) {
			throw new PermissionsError( 'edituser' );
		}

		$this->getOutput()->addWikiMsg( 'prefs-reset-intro' );

		$htmlForm = new HTMLForm( array(), $this->getContext(), 'prefs-restore' );

		$htmlForm->setSubmitTextMsg( 'restoreprefs' );
		$htmlForm->addHiddenField( 'username', $this->target );
		$htmlForm->addHiddenField( 'reset', '1' );
		$htmlForm->setSubmitCallback( array( $this, 'submitReset' ) );
		$htmlForm->suppressReset();

		$htmlForm->show();
	}

	public function submitReset( $formData ) {
		if ( !$this->getUser()->isAllowed( 'editmyoptions' ) ) {
			throw new PermissionsError( 'editmyoptions' );
		}

		if ( !$this->getUser()->isAllowed( 'edituser' ) ) {
			throw new PermissionsError( 'edituser' );
		}

		$this->targetuser->resetOptions( 'all', $this->getContext() );
		$this->targetuser->saveSettings();

		$url = $this->getTitle()->getFullURL( array( 'success' => 1, 'username' => $this->target ) );

		$this->getOutput()->redirect( $url );

		return true;
	}

	public function makeSearchForm() {
		global $wgScript;

		$fields = array();
		$fields['edituser-username'] = Html::input( 'username', $this->target );

		$thisTitle = $this->getTitle();
		$form = Html::rawElement( 'form', array( 'method' => 'get', 'action' => $wgScript ),
			Html::hidden( 'title', $this->getTitle()->getPrefixedDBkey() ) .
			Xml::buildForm( $fields, 'edituser-dosearch' )
		);
		return $form;
	}

	protected function getGroupName() {
		return 'users';
	}
}
