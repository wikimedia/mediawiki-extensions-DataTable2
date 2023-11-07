<?php

use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;
use MediaWiki\Page\Hook\ArticleDeleteHook;
use MediaWiki\Page\Hook\RevisionFromEditCompleteHook;

class DataTable2Hooks implements
	ArticleDeleteHook,
	LoadExtensionSchemaUpdatesHook,
	RevisionFromEditCompleteHook,
	ParserFirstCallInitHook
{

	/** @inheritDoc */
	public function onArticleDelete(
		WikiPage $wikiPage,
		\MediaWiki\User\User $user,
		&$reason,
		&$error,
		\MediaWiki\Status\Status &$status,
		$suppress
	) {
		DataTable2::singleton()->onArticleDelete( $wikiPage, $user, $reason, $error );
	}

	/** @inheritDoc */
	public function onLoadExtensionSchemaUpdates( $updater ) {
		DataTable2::singleton()->onLoadExtensionSchemaUpdates( $updater );
	}

	/** @inheritDoc */
	public function onParserFirstCallInit( $parser ) {
		DataTable2::singleton()->onParserFirstCallInit( $parser );
	}

	/** @inheritDoc */
	public function onRevisionFromEditComplete( $wikiPage, $rev, $originalRevId, $user, &$tags ) {
		DataTable2::singleton()->onRevisionFromEditComplete( $wikiPage, $rev, $originalRevId, $user );
	}

}
