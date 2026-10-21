<?php
/**
 * Class for DummyFandoomMainpageTags extension
 *
 * @file
 * @ingroup Extensions
 */

// DummyFandoomMainpageTags class
class DummyFandoomMainpageTags {

	/* Fields */

	private $mParser;
	private $mWidgetID = '';

	/* Functions */
	public static function onParserFirstCallInit( Parser &$parser ) {
		// Register the hook with the parser
		$parser->setHook( 'mainpage-leftcolumn-start', [ 'DummyFandoomMainpageTags', 'renderLeftColumn' ] );
		$parser->setHook( 'mainpage-rightcolumn-start', [ 'DummyFandoomMainpageTags', 'renderRightColumn' ] );
		$parser->setHook( 'mainpage-endcolumn', [ 'DummyFandoomMainpageTags', 'renderEndColumn' ] );

		// Continue
		return true;
	}

/*
<div class="main-page-tag-lcs main-page-tag-lcs-exploded" style="margin-right: -310px; "><div class="lcs-container" style="margin-right: 310px;">
</div></div>
<div class="main-page-tag-rcs"><div class="rcs-container">
</div></div>
*/
	public static function renderLeftColumn( $input, $args, Parser $parser ) {
		$htmlOut = Xml::openElement( 'div',
			[
				'class' => 'main-page-tag-lcs main-page-tag-lcs-exploded',
				'style' => 'margin-right: -310px;'
			]
		);
		$htmlOut .= Xml::openElement( 'div',
			[
				'class' => 'lcs-container',
				'style' => 'margin-right: 310px;'
			]
		);
		$parser->getOutput()->addModuleStyles( [
			'ext.dummyfandoommainpagetags.styles',
		] );
		return $htmlOut;
	}

	public static function renderRightColumn( $input, $args, Parser $parser ) {
		$htmlOut = Xml::openElement( 'div',
			[
				'class' => 'main-page-tag-rcs'
			]
		);
		$htmlOut .= Xml::openElement( 'div',
			[
				'class' => 'rcs-container'
			]
		);
		$parser->getOutput()->addModuleStyles( [
			'ext.dummyfandoommainpagetags.styles',
		] );
		return $htmlOut;
	}

	public static function renderEndColumn( $input, $args, Parser $parser ) {
		$htmlOut = Xml::closeElement( 'div' );
		$htmlOut .= Xml::closeElement( 'div' );
		return $htmlOut;
	}
}
