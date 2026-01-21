<?php
// Shared helpers for DNS/Apache manager

function base_dir(): string {
    return __DIR__ . '/etc/bind';
}

function named_conf_path(): string {
    return base_dir() . '/named.conf.local';
}

function zones_dir(): string {
    return base_dir() . '/zones';
}

function template_path(): string {
    return base_dir() . '/db.template';
}

function ensure_dirs(): void {
    if (!is_dir(zones_dir())) {
        mkdir(zones_dir(), 0755, true);
    }
    if (!file_exists(named_conf_path())) {
        touch(named_conf_path());
    }
}

function safe($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function list_domains(): array {
    $file = named_conf_path();
    if (!file_exists($file)) {
        return [];
    }
    $content = file_get_contents($file);
    preg_match_all('/zone\s+"([^"]+)"\s+{/', $content, $matches);
    return $matches[1] ?? [];
}

function add_domain_config(string $domain, string $ip): void {
    ensure_dirs();

    $named = named_conf_path();
    $template = template_path();
    $zoneFile = zones_dir() . '/db.' . $domain;

    $existingContent = file_exists($named) ? file_get_contents($named) : '';
    if (strpos($existingContent, 'zone "' . $domain . '"') !== false) {
        throw new Exception('Domain already exists');
    }

    if (!file_exists($template)) {
        throw new Exception('Template file not found');
    }

    $zoneConfig = "\nzone \"$domain\" {\n";
    $zoneConfig .= "    type master;\n";
    $zoneConfig .= "    file './etc/bind/zones/db.$domain';\n";
    $zoneConfig .= "};\n";

    if (file_put_contents($named, $zoneConfig, FILE_APPEND) === false) {
        throw new Exception('Failed to write to named.conf.local');
    }

    $zoneContent = file_get_contents($template);
    $zoneContent = str_replace('{DOMAIN}', $domain, $zoneContent);
    $zoneContent = str_replace('{IP_ADDRESS}', $ip, $zoneContent);

    if (file_put_contents($zoneFile, $zoneContent) === false) {
        throw new Exception('Failed to create zone file');
    }
}

function delete_domain_config(string $domain): void {
    $named = named_conf_path();
    $zoneFile = zones_dir() . '/db.' . $domain;

    if (file_exists($named)) {
        $content = file_get_contents($named);
        $pattern = '/zone\s+"' . preg_quote($domain, '/') . '"\s*{[^}]*}\s*;/s';
        $updated = preg_replace($pattern, '', $content);
        if ($updated === null) {
            throw new Exception('Failed to update named.conf.local');
        }
        file_put_contents($named, $updated);
    }

    if (file_exists($zoneFile)) {
        unlink($zoneFile);
    }
}

function parse_zone_records(string $domain): array {
    $path = zones_dir() . '/db.' . $domain;
    if (!file_exists($path)) {
        return ['header' => [], 'records' => []];
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    $header = [];
    $records = [];
    $afterSOA = false;

    foreach ($lines as $line) {
        $trim = trim($line);
        if ($trim === '') {
            if (!$afterSOA) {
                $header[] = $line;
            }
            continue;
        }
        if (substr($trim, 0, 1) === ';') {
            if (!$afterSOA) {
                $header[] = $line;
            }
            continue;
        }
        if (!$afterSOA) {
            $header[] = $line;
            if (str_contains($trim, ')')) {
                $afterSOA = true;
            }
            continue;
        }

        // parse record line
        $records[] = parse_record_line($line);
    }

    return ['header' => $header, 'records' => $records];
}

function parse_record_line(string $line): array {
    $trim = trim($line);
    $pattern = '/^(?P<name>\S+)\s+(?:(?P<ttl>\d+)\s+)?(?:(?:IN)\s+)?(?P<type>A|AAAA|CNAME|MX|NS|TXT)\s+(?P<rest>.+)$/i';
    if (!preg_match($pattern, $trim, $m)) {
        return ['raw' => $line, 'name' => $trim, 'type' => 'UNKNOWN', 'value' => $trim, 'ttl' => '', 'priority' => ''];
    }
    $name = $m['name'];
    $ttl = $m['ttl'] ?? '';
    $type = strtoupper($m['type']);
    $rest = trim($m['rest']);
    $priority = '';
    $value = $rest;

    if ($type === 'MX') {
        $parts = preg_split('/\s+/', $rest, 2);
        if (count($parts) === 2) {
            $priority = $parts[0];
            $value = $parts[1];
        }
    }

    return [
        'raw' => $line,
        'name' => $name,
        'type' => $type,
        'value' => $value,
        'ttl' => $ttl,
        'priority' => $priority,
    ];
}

function write_zone_records(string $domain, array $header, array $records): void {
    $path = zones_dir() . '/db.' . $domain;
    $lines = $header;
    if (!empty($lines) && trim(end($lines)) !== '') {
        $lines[] = '';
    }
    foreach ($records as $rec) {
        $name = $rec['name'] ?? '@';
        $ttl = $rec['ttl'] ?? '';
        $type = strtoupper($rec['type'] ?? 'A');
        $value = $rec['value'] ?? '';
        $priority = $rec['priority'] ?? '';

        $parts = [$name];
        if ($ttl !== '') { $parts[] = $ttl; }
        $parts[] = 'IN';
        $parts[] = $type;

        if ($type === 'MX' && $priority !== '') {
            $parts[] = $priority;
        }
        $parts[] = $value;

        $lines[] = implode("\t", $parts);
    }

    file_put_contents($path, implode("\n", $lines) . "\n");
}

function run_cmd(string $cmd): string {
    // Simple wrapper to capture output and errors using exec()
    $outputLines = [];
    $ret = 0;
    exec($cmd, $outputLines, $ret);
    $output = implode("\n", $outputLines);
    return $output === null ? '' : $output;
}
?>
