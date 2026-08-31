<?php
declare(strict_types=1);
/**
 * Lightweight source conformance check for the Core Blueprint starter.
 *
 * Usage:
 *   php tools/conformance.php
 */

$root = dirname( __DIR__ );
$failures = [];

/** @return string[] */
function cb_starter_files_with_extension( string $directory, string $extension ): array {
	if ( ! is_dir( $directory ) ) {
		return [];
	}

	$files = [];
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS )
	);

	foreach ( $iterator as $file ) {
		if ( ! $file instanceof SplFileInfo || ! $file->isFile() ) {
			continue;
		}
		if ( strtolower( $file->getExtension() ) === $extension ) {
			$files[] = $file->getPathname();
		}
	}

	sort( $files );
	return $files;
}

$php_files = array_merge(
	[ $root . '/core-blueprint-starter.php' ],
	cb_starter_files_with_extension( $root . '/src', 'php' )
);

$forbidden_php = [
	'cb-core-css-'                         => 'private Base CSS handles are not public API',
	'cb_core_event_labels'                 => 'legacy event-label mutation is not the Governance contract',
	'CB\\Core\\Log\\AuditLog'            => 'extensions must write through Governance\\Audit',
	'CB\\Core\\Admin\\AdminAssetCatalog' => 'the Base asset catalog is private',
	'add_menu_page('                       => 'Core Admin pages must register through PageRegistry',
	'add_submenu_page('                    => 'Core Admin pages must register through PageRegistry',
	'PageBase'                             => 'Base PageBase is not the normative public page contract',
	'Requires Plugins:'                    => 'first-party extensions use the runtime Base dependency guard, not a WordPress Requires Plugins header',
	'filemtime('                           => 'starter assets use the release version rather than request-time file timestamps',
	'jquery'                               => 'the starter is vanilla-JavaScript only',
];

foreach ( $php_files as $file ) {
	if ( ! is_file( $file ) ) {
		$failures[] = 'Missing expected runtime file: ' . str_replace( $root . '/', '', $file );
		continue;
	}

	$content = (string) file_get_contents( $file );
	foreach ( $forbidden_php as $needle => $reason ) {
		if ( false !== stripos( $content, $needle ) ) {
			$failures[] = sprintf(
				'%s contains forbidden pattern "%s" (%s).',
				str_replace( $root . '/', '', $file ),
				$needle,
				$reason
			);
		}
	}
}

$base_appearance_property = '/(^|;)\s*(?:background(?:-[a-z-]+)?|border(?:-[a-z-]+)?|border-radius|box-shadow|color|font(?:-[a-z-]+)?|padding(?:-[a-z-]+)?|text-shadow|outline(?:-[a-z-]+)?|transition(?:-[a-z-]+)?)\s*:/im';

foreach ( cb_starter_files_with_extension( $root . '/assets/css', 'css' ) as $file ) {
	$content = (string) file_get_contents( $file );
	if ( ! preg_match_all( '/([^{}]+)\{([^{}]*)\}/s', $content, $rules, PREG_SET_ORDER ) ) {
		continue;
	}

	foreach ( $rules as $rule ) {
		$selector     = trim( (string) $rule[1] );
		$declarations = (string) $rule[2];
		if ( ! str_contains( $selector, '.cb-core-' ) ) {
			continue;
		}
		if ( preg_match( $base_appearance_property, $declarations ) ) {
			$failures[] = sprintf(
				'%s redraws Base-owned appearance in selector "%s". Composition/layout is allowed; canonical appearance stays in Base.',
				str_replace( $root . '/', '', $file ),
				preg_replace( '/\s+/', ' ', $selector )
			);
		}
	}
}

if ( ! empty( $failures ) ) {
	fwrite( STDERR, "Core Blueprint starter conformance: FAIL\n\n" );
	foreach ( $failures as $failure ) {
		fwrite( STDERR, '- ' . $failure . "\n" );
	}
	exit( 1 );
}

fwrite( STDOUT, "Core Blueprint starter conformance: PASS\n" );
exit( 0 );
