<?php

namespace MediaWiki\Extension\Sitenotice2;

use Html;
use MediaWiki\Parser\Sanitizer;
use Skin;
use Xml;

class Hooks {

    /**
     * @param string &$notice
     * @param Skin $skin
     * @suppress SecurityCheck-DoubleEscaped
     */
    public static function onSiteNoticeAfter( &$notice, $skin ) {
        global $wgMajorSiteNoticeID, $wgDismissableSiteNoticeForAnons, $wgSitename, $wgLogos;
        if ( method_exists( $skin, 'getVersion' ) ) {
            // does the skin support versioning and if so does it provide dismissable site notices?
            if ( $skin->getVersion() === 2 ) {
                return;
            }
        }

        if ( !$notice ) {
            return;
        }

        // Dismissal for anons is configurable
        if ( $wgDismissableSiteNoticeForAnons || $skin->getUser()->isRegistered() ) {
            // Cookie value consists of two parts
            $major = (int)$wgMajorSiteNoticeID;
            $minor = (int)$skin->msg( 'sitenotice_id' )->inContentLanguage()->text();

            $skinName = $skin->getSkinName();
            $out = $skin->getOutput();

            // Cargar estilos dependiendo de la skin
            switch ($skinName) {
                case 'citizen':
                    $out->addModuleStyles( 'ext.dismissableSiteNotice.citizen.styles' );
                    break;
                case 'cosmos':
                    $out->addModuleStyles( 'ext.dismissableSiteNotice.cosmos.styles' );
                case 'minerva':
                    $out->addModuleStyles( 'ext.dismissableSiteNotice.minerva.styles' );
                    break;
                case 'vector':
                    $out->addModuleStyles( 'ext.dismissableSiteNotice.vector.styles' );
                    break;
                default: # Default Styles for All Skins
                    $out->addModuleStyles( 'ext.dismissableSiteNotice.styles' );
            }
            # JS for all Skins.
            $out->addModules( 'ext.dismissableSiteNotice' );
            $out->addJsConfigVars( 'wgSiteNoticeId', "$major.$minor" );

            $noticeCloseMsg = $skin->msg( 'sitenotice_close' )->text();
            $wikiName = htmlspecialchars( $wgSitename );

            // Obtener el icono del wiki desde $wgLogos
            $wikiIcon = '';
            if ( isset( $wgLogos['icon'] ) ) {
                $iconUrl = $wgLogos['icon'];
                $wikiIcon = Html::element( 'img', [
                    'src' => htmlspecialchars( $iconUrl ),
                    'class' => 'sitenotice2-icon',
                    'alt' => $wikiName,
                    'style' => 'height: 24px; width: 24px;'
                ] );
            }

            $notice = Html::rawElement( 'dialog', [ 'class' => 'mw-sitenotice2' ],
                Html::rawElement( 'div', [ 'class' => 'mw-sitenotice2-header' ],
				Html::rawElement( 'div', [ 'class' => 'mw-sitenotice-icon' ], $wikiIcon ) . // Icono del wiki
                    Html::element( 'h2', [ ], $wikiName ) . // Nombre del wiki
                    Html::rawElement( 'div', [ 'class' => 'mw-dismissable-notice-close' ],
                        sprintf(
                            '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20"><title>%s</title><path d="m4.34 2.93 12.73 12.73-1.41 1.41L2.93 4.35z"/><path d="M17.07 4.34 4.34 17.07l-1.41-1.41L15.66 2.93z"/></svg>',
                            htmlspecialchars( $noticeCloseMsg )
						)
					)
				) .
				Html::rawElement( 'div', [ 'class' => 'mw-sitenotice2-body' ], $notice )
			);
			
			// Si la skin es "cosmos", elimina los elementos específicos
			if ($skinName === 'cosmos') {
				$notice = preg_replace('/<div id="cosmos-content-siteNotice">.*?<\/div>/s', '', $notice);
				$notice = preg_replace('/<div class="cosmos-button cosmos-button-primary">.*?<\/div>/s', '', $notice);
			}
		}

        if ( $skin->getUser()->isAnon() ) {
            // Hide the sitenotice from search engines (see bug T11209 comment 4)
            // NOTE: Is this actually effective?
            // NOTE: Avoid document.write (T125323)
            // NOTE: Must be compatible with JavaScript in ancient Grade C browsers.
            $jsWrapped =
                '<div id="mw-dismissablenotice-anonplace"></div>' .
                Html::inlineScript(
                    '(function(){' .
                    'var node=document.getElementById("mw-dismissablenotice-anonplace");' .
                    'if(node){' .
                    // Replace placeholder with parsed HTML from $notice.
                    // Setting outerHTML is supported in all Grade C browsers
                    // and gracefully fallsback to just setting a property.
                    // It is short for:
                    // - Create temporary element or document fragment
                    // - Set innerHTML.
                    // - Replace node with wrapper's child nodes.
                    'node.outerHTML=' . Xml::encodeJsVar( $notice ) . ';' .
                    '}' .
                    '}());'
                );
            $notice = $jsWrapped;
        }
    }
}
