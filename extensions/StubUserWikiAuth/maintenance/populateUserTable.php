<?php
/**
 * Populates the user table with information from other tables.
 * Useful to fill the user table with stub data after importing
 * content from other grabbers.
 *
 * @file
 * @ingroup Maintenance
 * @author Jesús Martínez <martineznovo@gmail.com>
 * @version 1.2.0
 * @date 19 August 2020
 */

/**
 * Set the correct include path for PHP
 */
ini_set( 'include_path', __DIR__ . '/../../../maintenance' );

require_once 'Maintenance.php';

use MediaWiki\MediaWikiServices;
use Wikimedia\IPUtils;

class PopulateUserTable extends Maintenance {

	/**
	 * List of tables to populate users from
	 *
	 * @var array
	 */
	protected $tables;

	/**
	 * Array of valid tables and columns that contain user id and name
	 *
	 * @var array
	 */
	protected $validTables = [
			'revision' => [
				'id' => 'rev_user',
				'name' => 'rev_user_text'
			],
			'logging' => [
				'id' => 'log_user',
				'name' => 'log_user_text'
			],
			'image' => [
				'id' => 'img_user',
				'name' => 'img_user_text'
			],
			'oldimage' => [
				'id' => 'oi_user',
				'name' => 'oi_user_text'
			],
			'filearchive' => [
				'id' => 'fa_user',
				'name' => 'fa_user_text'
			],
			'archive' => [
				'id' => 'ar_user',
				'name' => 'ar_user_text'
			],
			'ipblocks' => [
				'id' => 'ipb_by',
				'name' => 'ipb_by_text'
			],
			'actor' => [
				'id' => 'actor_id',
				'name' => 'actor_name'
			]
		];

	/**
	 * Handle to the database connection
	 *
	 * @var DatabaseBase
	 */
	protected $dbw;

	/**
	 * MediaWikiBot instance
	 *
	 * @var MediaWikiBot
	 */
	protected $bot;

	public function __construct() {
		parent::__construct();
		$this->addDescription( 'Populates the user table creating stub users (user ID and name) from other tables.' );
		$this->addOption( 'db', 'Database name, if we don\'t want to write to $wgDBname', false /* required? */, true /* withArg */ );
		$this->addOption( 'tables', 'Tables to grab users from (pipe separated list) revision, logging, image, oldimage, filearchive, archive, ipblocks, actor', false, true );
	}

	public function execute() {
		global $wgDBname, $wgActorTableSchemaMigrationStage;

		# Get a DB connection
		$dbProvider = MediaWikiServices::getInstance()->getDBLoadBalancerFactory();
		$this->dbw = $dbProvider->getMainLB()->getConnection( DB_PRIMARY, [], $this->getOption( 'db', $wgDBname ) );

		$useActorTable = false;

		if ( class_exists( 'ActorMigration' ) && !isset( $wgActorTableSchemaMigrationStage ) ) {
			// MediaWiki 1.34+
			$useActorTable = true;
		} elseif ( defined( 'SCHEMA_COMPAT_OLD' ) ) {
			// MediaWiki 1.32+
			if ( ( $wgActorTableSchemaMigrationStage | SCHEMA_COMPAT_OLD ) != SCHEMA_COMPAT_OLD ) {
				$useActorTable = true;
			}
		} elseif ( defined( 'MIGRATION_OLD' ) && ( $wgActorTableSchemaMigrationStage | MIGRATION_OLD ) != MIGRATION_OLD ) {
			// MediaWiki 1.31
			$useActorTable = true;
		}

		$tables = $this->getOption( 'tables' );
		if ( $useActorTable ) {
			# If the schema migration is set to use the actor table, only use the actor table
			if ( !is_null( $tables ) ) {
				$this->error( 'The "tables" parameter can\'t be provided when the actor table is being used.', 1 );
			}
			$this->tables = [ 'actor' ];
		} else {
			unset( $this->validTables['actor'] );

			if ( !is_null( $tables ) ) {
				$this->tables = explode( '|', $tables );

				# Check that no invalid tables were provided
				$invalidTables = array_diff( $this->tables, array_keys( $this->validTables ) );
				if ( count( $invalidTables ) > 0 ) {
					$this->error( sprintf( 'Invalid tables provided: %s',
						implode( ',', $invalidTables ) ), 1 );
				}
			} else {
				$this->tables = array_keys( $this->validTables );
			}
		}

		foreach ( $this->tables as $table ) {
			$this->populateUsersFromTable( $table );
		}

		if ( $useActorTable ) {
			$this->populateUserIds();
		}
	}

	function populateUsersFromTable( $table ) {
		$columnInfo = $this->validTables[$table];
		$userIDField = $columnInfo['id'];
		$userNameField = $columnInfo['name'];

		# Dummy values for all required fields
		$e = [
			'user_id' => '',
			'user_name' => '',
			'user_real_name' => '',
			'user_password' => '',
			'user_newpassword' => '',
			'user_email' => '',
			'user_touched' => wfTimestampNow(),
			'user_token' => ''
		];

		$this->output( "Populating users from table $table..." );

		$count = 0;
		$lastUserId = 0;

		while ( true ) {
			$result = $this->dbw->select(
				[ $table, 'user' ],
				[ $userIDField, $userNameField ],
				[
					'user_id' => null,
					"$userIDField > $lastUserId"
				],
				__FUNCTION__,
				[
					'DISTINCT',
					'ORDER BY' => $userIDField,
					'LIMIT' => $this->mBatchSize,
				],
				[
					'user' => [ 'LEFT JOIN', "user_id=$userIDField" ]
				]
			);

			if ( !$result->numRows() ) {
				break;
			}

			$row = $result->fetchRow();
			while ( $row ) {
				$lastUserId = $row[$userIDField];
				$e['user_id'] = $lastUserId;
				$e['user_name'] = $row[$userNameField];

				# Don't create users for IP addresses
				if ( IPUtils::isIPAddress( $e['user_name'] ) ) {
					$row = $result->fetchRow();
					continue;
				}

				$inserted = $this->dbw->insert(
					'user',
					$e,
					__METHOD__,
					# If there have been a rename in the middle, can be
					# duplicate ID for different user names
					[ 'IGNORE' ]
				);
				if ( $inserted ) {
					$count++;
					if ( $count % 500 == 0 ) {
						$this->output( "$count insertions...\n" );
					}
				}
				$row = $result->fetchRow();
			}
		}
		$this->output( "Done: $count insertions.\n" );
	}

	function populateUserIds () {
		$this->output( "Populating user IDs in actor table..." );
		$count = 0;
		$lastActorId = 0;

		while ( true ) {
			# Get all actors without an associated user ID
			$result = $this->dbw->newSelectQueryBuilder()
				->select( [ 'actor_id', 'actor_name' ] )
				->from( 'actor' )
				->where( [
					'actor_user IS NULL',
					"actor_id > $lastActorId"
				] )
				->orderBy( 'actor_id' )
				->limit( $this->mBatchSize )
				->caller( __METHOD__ )
				->fetchResultSet();

			if ( !$result->numRows() ) {
				break;
			}

			while ( $row = $result->fetchRow() ) {
				$lastActorId = $row['actor_id'];

				# Try to get a user with the actor's name
				$userResult = $this->dbw->newSelectQueryBuilder()
					->select( [ 'user_name', 'user_id' ] )
					->from( 'user' )
					->where( [
						'user_name = ' . $this->dbw->addQuotes( $row['actor_name'] ),
						'user_password = ' . $this->dbw->addQuotes( '' )
					] )
					->caller( __METHOD__ )
					->fetchResultSet();

				$user = $userResult->fetchRow();

				if ( $user ) {
					$this->output( "Updating actor {$row['actor_name']} ({$row['actor_id']}) with user ID {$user['user_id']}\n" );

					$this->dbw->update(
						'actor',
						[ 'actor_user' => $user['user_id'] ],
						[ 'actor_id' => $row['actor_id'] ],
						__METHOD__
					);

					$count++;
					if ( $count % 500 == 0 ) {
						$this->output( "$count actors updated...\n" );
					}
				}
			}
		}

		$this->output( "Done: $count actors updated.\n" );
	}
}

$maintClass = 'PopulateUserTable';
require_once RUN_MAINTENANCE_IF_MAIN;
