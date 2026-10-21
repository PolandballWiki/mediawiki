<?php
class LessIntegrationHooks {
	public static function onCodeEditorGetPageLanguage( $title, &$lang ) {
		if ( $title->getNamespace() === NS_MEDIAWIKI && preg_match( '/\.less$/', $title->getText() )) {
			$lang = 'less';
			return false;
		}

		return true;
	}

	public static function onContentHandlerDefaultModelFor( Title $title, &$model ) {
		if ( $title->getNamespace() === NS_MEDIAWIKI && preg_match( '/\.less$/', $title->getText() )) {
			$model = 'less';
			return false;
		}

		return true;
	}
}
