<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

/**
 * @brief Special page DataTable2Tables for the @ref
 * Extensions-DataTable2.
 *
 * @file
 *
 * @ingroup Extensions
 * @ingroup Extensions-DataTable2
 *
 * @author [RV1971](https://www.mediawiki.org/wiki/User:RV1971)
 *
 * @sa Largely inspired by SpecialListusers.php.
 */

/**
 * @brief Pager used in SpecialDataTable2Tables.
 *
 * @ingroup Extensions-DataTable2
 *
 * @sa [MediaWiki Manual:Pager.php]
 * (https://www.mediawiki.org/wiki/Manual:Pager.php)
 */
class DataTable2TablesPager extends DataTable2Pager {

	/* == magic methods == */

	/**
	 * @brief Constructor.
	 *
	 * @param IContextSource $context Context.
	 *
	 * @param string $par Table name to start from.
	 *
	 * @xrefitem userdoc "User Documentation" "User Documentation" The
	 * special page <b>DataTable2Tables</b> accepts one parameter,
	 * which can either be appended to the URL with a slash
	 * (e.g. Special:DataTable2Tables/Employees) or given as the
	 * REQUEST parameter <tt>tablename</tt>. The former takes
	 * precedence. The page will display tables whose names are
	 * greater or equal to this.
	 */
	public function __construct( IContextSource $context = null,
		$tablename = null ) {

		parent::__construct( $context, $tablename );
	}

	/* == overriding methods == */

	/// Specify the database query to be run by AlphabeticPager.
	public function getQueryInfo() {
		global $wgDataTable2ReadSrc;

		$conds = array();

		$dbr = wfGetDB( DB_SLAVE );

		if ( $this->tablename != '' ) {
			$conds[] = 'dtd_table >= '
				. $dbr->addQuotes( $this->tableDbKey );
		}

		$table = $dbr->selectSQLText( $wgDataTable2ReadSrc,
			array( 'dtd_table', 'dtd_page', 'records' => 'count(*)' ),
			$conds, __METHOD__,
			array( 'GROUP BY' => array( 'dtd_table', 'dtd_page' ) ) );

		return array( 'tables' => array( 'd' => "($table)" ),
			'fields' => array( 'dtd_table', 'pages' => 'count(*)',
				'records' => 'sum(records)' ),
			'options' => array( 'GROUP BY' => 'dtd_table' )
		);
	}

	/// Specify `dtd_table` as the index field for AlphabeticPager.
	public function getIndexField() {
		return 'dtd_table';
	}

	/**
	 * @brief Format a data row.
	 *
	 * @param stdClass $row Database row object.
	 *
	 * @return *string* Wikitext.
	 */
	public function formatRow( $row ) {
		$table = Title::makeTitle( NS_MAIN, $row->dtd_table );


		$detailCateg = $this->msg( 'datatable2-consumer-detail-category',
			$table->getText() )->inContentLanguage()->text();

		return $this->msg( 'datatable2tables-row', $table->getText(),
			$row->pages, $row->records, $detailCateg )->text();
	}

	/**
	 * @brief Provide the page header, which contains a form to select data.
	 *
	 * @return *string* html code.
	 */
	public function getPageHeader( ) {
		$content = Html::rawElement( 'label',
			array( 'for' => 'tablename' ),
			$this->msg( 'datatable2tables-from' )->parse() ) . '&#160'
			. Xml::input( 'tablename', 25, $this->tablename,
				array( 'id' => 'tablename' ) ) . ' ';

		return $this->buildPageHeader( 'tables', $content );
	}
}

/**
 * @brief Special page DataTable2Tables for the @ref
 * Extensions-DataTable2.
 *
 * @ingroup Extensions-DataTable2
 */
class SpecialDataTable2Tables extends SpecialDataTable2
{
	/// Constructor.
	public function __construct() {
		parent::__construct( 'DataTable2Tables', false );
	}
}
