<?php
namespace MediaWiki\Extension\StructuredData;

use Config;
use MediaWiki\MainConfigNames;
use OutputPage;

class Hooks implements
    \MediaWiki\Hook\BeforePageDisplayHook
{

    /** @var Config */
    private Config $config;

    /**
     * @param Config $config
     */
    public function __construct( Config $config ) {
        $this->config = $config;
    }

    /**
     * Inserts structured data into a page.
     * @param OutputPage $out
     * @param Skin $skin
     */
    public function onBeforePageDisplay( $out, $skin ): void {
        $structuredData = $this->makeStructuredData( $out );
        if ( $structuredData ) {
            $out->addHeadItem(
                'StructuredData',
                '<script type="application/ld+json">' . json_encode( $structuredData ) . '</script>'
            );
        }
    }

    /**
     * Constructs structured data for an OutputPage.
     *
     * Currently only produces sitelinks search boxes on the main page.
     *
     * TODO: article structured data
     *
     * @param OutputPage $out
     * @return array|null
     */
    private function makeStructuredData( OutputPage $out ): ?array {
        $canonicalServer = $this->config->get( MainConfigNames::CanonicalServer );

        if ( $out->getTitle()->isMainPage() ) {
            $articlePath = $this->config->get( MainConfigNames::ArticlePath );
            return [
                '@context'        => 'http://schema.org',
                '@type'           => 'WebSite',
                'url'             => $canonicalServer,
                'potentialAction' => [
                    '@type'       => 'SearchAction',
                    'target'      => wfAppendQuery(
                        $canonicalServer . str_replace( '$1', 'Special:Search', $articlePath ),
                        'search={search_term_string}'
                    ),
                    'query-input' => 'required name=search_term_string',
                ],
            ];
        }

        return null;
    }
}
