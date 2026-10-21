<?php
class LessIntegrationContentHandler extends TextContentHandler {
	public function __construct( $modelId = 'less' ) {
		parent::__construct( $modelId, [ 'less' ] );
	}

	protected function getContentClass() {
		return LessIntegrationContent::class;
	}
}
