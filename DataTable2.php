<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo( "This is an extension to the MediaWiki package and cannot be run standalone.\n" );
	die( -1 );
}

/**
 * @brief DataTable2 MediaWiki extension.
 *
 * @defgroup Extensions-DataTable2 DataTable2 extension
 *
 * @ingroup Extensions
 *
 * To activate this extension, put the source files into
 * `$IP/extensions/DataTable2` and add the following into your
 * `LocalSettings.php` file:
 *
 * ~~~
 * require_once("$IP/extensions/DataTable2/DataTable2.php");
 * ~~~
 *
 * You can customize @ref $wgDataTable2WriteDest, @ref
 * $wgDataTable2ReadSrc, @ref $wgDataTable2MetaWriteDest, @ref
 * $wgDataTable2MetaReadSrc, @ref $wgDataTable2Args, @ref
 * $wgDataTable2SqlWhiteList, @ref $wgSpecialDataTable2PageParSep,
 * @ref $wgGroupPermissions and the @ref DataTable2.i18n.php
 * "messages".
 *
 * @version 1.0.3
 *
 * @copyright [GPL-3.0+](https://gnu.org/licenses/gpl-3.0-standalone.html)
 *
 * @author [RV1971](http://www.mediawiki.org/wiki/User:RV1971)
 *
 * @sa User documentation:
 * - [on mediawiki.org](http://www.mediawiki.org/wiki/Extension:DataTable2)
 * - @ref userdoc "extracted by doxygen"
 *
 * @sa [MediaWiki Manual](http://www.mediawiki.org/wiki/Manual:Contents):
 * - [Developing extensions]
 * (http://www.mediawiki.org/wiki/Manual:Developing_extensions)
 * - [Tag extensions](http://www.mediawiki.org/wiki/Manual:Tag extensions)
 * - [Parser functions](http://www.mediawiki.org/wiki/Manual:Parser functions)
 * - [Special pages](http://www.mediawiki.org/wiki/Manual:Special pages)
 * - [Magic words](http://www.mediawiki.org/wiki/Manual:Magic words)
 * - [Hooks](http://www.mediawiki.org/wiki/Manual:Hooks)
 * - [Messages API](http://www.mediawiki.org/wiki/Manual:Messages_API)
 * - [Database access](http://www.mediawiki.org/wiki/Manual:Database access)
 * - [Profiling](http://www.mediawiki.org/wiki/Manual:How_to_debug#Profiling)
 *
 * @sa Other links:
 * - [Semantic Versioning](http://semver.org)
 *
 * @todo Add language fr.
 *
 * @todo Provide sql files for other backends.
 *
 * @todo Publish sample use cases, e.g. templates replacing the Task
 * Extension.
 */

/**
 * @brief Setup for the @ref Extensions-DataTable2.
 *
 * @file
 *
 * @ingroup Extensions
 * @ingroup Extensions-DataTable2
 *
 * @author [RV1971](http://www.mediawiki.org/wiki/User:RV1971)
 *
 */

/// Name of the database table where data is written to.
$wgDataTable2WriteDest = 'datatable2_data';

/**
 * @brief Name of the database table where data is read from.
 *
 * Will be set to @ref $wgDataTable2WriteDest in DataTable2::init() if unset.
 *
 * You might set this to a view which is a union of
 * $wgDataTable2WriteDest and data from other sources which can then
 * be read but not modified through this extension. In such a case,
 * the data from other sources should leave the `dtd_page` column
 * empty (NULL).
 */
$wgDataTable2ReadSrc = null;

/// Name of the database table where meta data is written to.
$wgDataTable2MetaWriteDest = 'datatable2_meta';

/**
 * @brief Name of the table where meta data is read from.
 *
 * Will be set to @ref $wgDataTable2MetaWriteDest in
 * DataTable2::init() if unset.
 *
 * You might set this to a view which is a union of
 * $wgDataTable2MetaWriteDest and data from other sources which can
 * then be read but not modified through this extension.
 */
$wgDataTable2MetaReadSrc = null;

/**
 * @brief Default arguments for datatable2 tags.
 *
 * @sa DataTable2Parser::getArg() for a description of valid arguments.
 */
$wgDataTable2Args = array(
	'fs' => '|',
	'rs' => '/[\n\r]+/'
);

/**
 * @brief Array of identifiers that may be used in WHERE and ORDER BY
 * clauses, in addition to column names.
 *
 * Unquoted identifiers in `where` and `order-by` arguments are
 * converted to uppercase, hence the items in
 * $wgDataTable2SqlWhiteList should be uppercase unless they are
 * deliberately case-sensitive. The default contains only some rather
 * portable SQL functions, which are probably a small subset of those
 * available in your database backend. Hence you are most likely to
 * add functions to this. Since the dot is considered a valid
 * character for an identifier token, you may add qualified names like
 * functions in packages or other schemas.
 */
$wgDataTable2SqlWhiteList = array(
	// order directions
	'ASC', 'DESC', 'NULLS', 'FIRST', 'LAST',
	// logical operators
	'AND', 'NOT', 'OR',
	// predicates
	'BETWEEN', 'IN', 'IS', 'LIKE', 'NULL',
	// CASE expressions
	'CASE', 'WHEN', 'THEN', 'ELSE', 'END', 'COALESCE', 'NULLIF',
	// cast expressions
	'CAST', 'AS',
	// some rather portable SQL functions
	'EXTRACT', 'FROM',
	'ABS', 'MOD', 'LN', 'EXP', 'POWER', 'SQRT', 'FLOOR', 'CEIL',
	'SUBSTR', 'SUBSTRING', 'UPPER', 'LOWER', 'TRANSLATE', 'TRIM'
);

/**
 * @brief Separator for parameters passed to special pages.
 *
 * Parameters can be passed to special pages as subpage
 * components. When more than one parameter is possible and a
 * parameter could be a page name with a subpage, the slash cannot be
 * used to separate the parameters. There is no obvious choice for the
 * best separator since the characters which by default are not
 * allowed in page titles cannot be used in internal links,
 * either.
 *
 * @note If you modify this, you will have to modify any messages
 * which contain links to DataTable2 special pages passing more than
 * one parameter. In the default configuration, this is the case for
 * the message `datatable2pages-row`.
 */
$wgSpecialDataTable2PageParSep = '//';

/**
 * @brief Array of css classes for the table used in
 * Special:DataTable2Data.
 */
$wgSpecialDataTable2DataClasses = array( 'wikitable', 'sortable' );

/**
 * @brief [About]
 * (http://www.mediawiki.org/wiki/$wgExtensionCredits) this extension.
 */
$wgExtensionCredits['parserhook'][] = $wgExtensionCredits['specialpage'][] =
	array(
		'path' => __FILE__,
		'name' => 'DataTable2',
		'descriptionmsg' => 'datatable2-desc',
		'version' => '1.0.3',
		'author' => '[http://www.mediawiki.org/wiki/User:RV1971 RV1971]',
		'url' => 'http://www.mediawiki.org/wiki/Extension:DataTable2'
	);

/**
 * @brief Define a [new
 * right](http://www.mediawiki.org/wiki/$wgAvailableRights) to use the
 * DataTable2 special pages.
 */
$wgAvailableRights[] = 'datatable2-specialpages';

/** * @brief [Assign the
 * right](http://www.mediawiki.org/wiki/Manual:$wgGroupPermissions) to
 * use DataTable2 special pages to all registered users by default.
 *
 * @xrefitem userdoc "User Documentation" "User Documentation" This
 * extension defines a <b>new right</b> <tt>datatable2-specialpages</tt>
 * needed to use the special pages, which is assigned by default to
 * all registered users. You might decide to restrict this if you have
 * so many DataTable2 data that use of the special pages degrades
 * performance. It is pointless to restrict this for security reasons
 * since all DataTable2 data can be extracted using the
 * \<dt2-showtable> tag anyway.
 */
$wgGroupPermissions['user']['datatable2-specialpages'] = true;

/** @cond */
foreach ( array( 'DataTable2' => 'DataTable2.body',
		'DataTable2Database',
		'DataTable2Exception',
		'DataTable2Parser',
		'DataTable2ParserWithRecords' => 'DataTable2Parser',
		'DataTable2SqlTransformer',
		'Scribunto_LuaDataTable2Library',
		'SpecialDataTable2',
		'DataTable2Pager' => 'SpecialDataTable2',
		'SpecialDataTable2Data',
		'SpecialDataTable2Pages',
		'SpecialDataTable2Tables'
	) as $class => $file ) {
	if ( is_int( $class ) ) {
		$class = $file;
	}
	/** @endcond */

	/** @brief [Autoloading]
	 * (http://www.mediawiki.org/wiki/Manual:$wgAutoloadClasses)
	 * classes. */
	$wgAutoloadClasses[$class] = __DIR__ . "/$file.php";
}

/**
 * @brief [Defer initialization]
 * (https://www.mediawiki.org/wiki/Manual:$wgExtensionFunctions)
 */
$wgExtensionFunctions[] = 'DataTable2::init';

/// [Localisation](https://www.mediawiki.org/wiki/Localisation_file_format).
$wgMessagesDirs['DataTable2'] = __DIR__ . '/i18n';

/**
 * @brief Old-style [Localisation]
 * (http://www.mediawiki.org/wiki/Localisation) file for MW 1.19 compatibility.
 */
$wgExtensionMessagesFiles['DataTable2'] = __DIR__ . '/DataTable2.i18n.php';

/// [Magic words](http://www.mediawiki.org/wiki/Magic words) file.
$wgExtensionMessagesFiles['DataTable2Magic'] =
	__DIR__ . '/DataTable2.i18n.magic.php';

/**
 * @brief [Aliases]
 * (http://www.mediawiki.org/wiki/Manual:Special_pages#The_Aliases_File) file
 */
$wgExtensionMessagesFiles['DataTable2Alias'] =
  __DIR__ . '/DataTable2.alias.php';

/**
 * @brief [Special page]
 * (http://www.mediawiki.org/wiki/Manual:Special pages) DataTable2Data.
 */
$wgSpecialPages['DataTable2Data'] = 'SpecialDataTable2Data';

/**
 * @brief [Special page]
 * (http://www.mediawiki.org/wiki/Manual:Special pages) DataTable2Pages.
 */
$wgSpecialPages['DataTable2Pages'] = 'SpecialDataTable2Pages';

/**
 * @brief [Special page]
 * (http://www.mediawiki.org/wiki/Manual:Special pages) DataTable2Tables.
 */
$wgSpecialPages['DataTable2Tables'] = 'SpecialDataTable2Tables';
?>