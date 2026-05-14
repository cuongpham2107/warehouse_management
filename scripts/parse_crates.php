<?php

$input = <<<'EOD'
Crate 1 6/30/2025
Crate 2 6/30/2025
Crate 3 6/30/2025
Crate 4 6/30/2025
Crate 5 6/30/2025
Crate 6 6/30/2025
Crate 7 6/30/2025
Crate 8 6/30/2025
Crate 9 6/30/2025
Crate 10 6/30/2025
Crate 11 6/30/2025
Crate 12 6/30/2025
Crate 13 6/30/2025
Crate 14 6/30/2025
EOD;

$raw = trim($input);
$lines = preg_split('/\r\n|\r|\n/', $raw);
$lines = array_filter(array_map('trim', $lines), fn ($v) => $v !== '');

$pkCandidates = [];
$codeCandidates = [];
foreach ($lines as $line) {
    $line = preg_replace('/[\x{00A0}]/u', ' ', $line);
    $clean = preg_replace([
        '/\\b\\d{1,2}\\/\\d{1,2}\\/\\d{4}\\b/',
        '/\\b\\d{4}-\\d{2}-\\d{2}\\b/',
    ], '', $line);
    $clean = trim(preg_replace('/\\s+/', ' ', $clean));

    if (preg_match('/\\b[Cc]rate[\\s-]*(\\d+)\\b/u', $line, $m)) {
        $pkCandidates[] = (int) $m[1];

        continue;
    }

    if (preg_match('/^\\d+$/', $clean, $m2)) {
        $pkCandidates[] = (int) $clean;

        continue;
    }

    if ($clean !== '') {
        $codeCandidates[] = $clean;
    }
}

$pkCandidates = array_values(array_unique(array_filter($pkCandidates, fn ($v) => $v)));
$codeCandidates = array_values(array_unique(array_filter(array_map('trim', $codeCandidates), fn ($v) => $v !== '')));

echo 'Lines count: '.count($lines).PHP_EOL;
echo "--- Lines ---\n".implode("\n", $lines).PHP_EOL.PHP_EOL;
echo "--- PK candidates (match crates.id) ---\n".implode("\n", $pkCandidates).PHP_EOL.PHP_EOL;
echo "--- Code candidates (match crates.crate_id) ---\n".implode("\n", $codeCandidates).PHP_EOL;
