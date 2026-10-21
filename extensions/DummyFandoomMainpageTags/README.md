# mediawiki-extensions-DummyFandoomMainpageTags
MediaWiki extension that define tags used by FANDOOM (wikia) on the main page of wikis

FANDOOM use 3 XML-style tags on the main page of their wikis, for layout and advertisement placeholders: 

- mainpage-leftcolumn-start
- mainpage-rightcolumn-start
- mainpage-endcolumn

This extension defines them, to allow easy migration for wikis that want to abandon FANDOOM and host their wikis on their own server. It adds the basic CSS styles needed to display the main page correctly.

Ideally, you should stop using those tags and use standard HTML with your custom styling, but this brings you an inmediate replacement until then.
