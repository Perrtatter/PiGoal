<?php
header('Content-Type: application/json');

$scriptPath = realpath(__DIR__ . '/server.py');
$pidFile = __DIR__ . '/.server.pid';

function is_server_process(int $pid, string $scriptPath): bool
{
	exec('ps -ww -p ' . $pid . ' -o command=', $output, $exitCode);
	if ($exitCode !== 0 || !isset($output[0])) {
		return false;
	}

	if (strpos($output[0], $scriptPath) !== false) {
		return true;
	}
	if (strpos($output[0], basename($scriptPath)) === false) {
		return false;
	}

	exec('lsof -a -p ' . $pid . ' -d cwd -Fn', $workingDirectory);
	return in_array('n' . dirname($scriptPath), $workingDirectory, true);
}

function find_server_process(string $scriptPath, string $pidFile): ?int
{
	if (is_file($pidFile)) {
		$pid = (int) file_get_contents($pidFile);
		if ($pid > 0 && is_server_process($pid, $scriptPath)) {
			return $pid;
		}
		@unlink($pidFile);
	}

	exec('lsof -nP -iTCP:8765 -sTCP:LISTEN -t', $pids);
	foreach ($pids as $candidate) {
		$pid = (int) $candidate;
		if ($pid > 0 && is_server_process($pid, $scriptPath)) {
			file_put_contents($pidFile, (string) $pid);
			return $pid;
		}
	}

	return null;
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'start';
$pid = find_server_process($scriptPath, $pidFile);

if ($action === 'stop') {
	if ($pid !== null) {
		posix_kill($pid, SIGTERM);
		$deadline = microtime(true) + 3;
		while (posix_kill($pid, 0) && microtime(true) < $deadline) {
			usleep(50000);
		}
	}
	@unlink($pidFile);
	echo json_encode(['stopped' => true]);
	exit;
}

if ($action !== 'start') {
	http_response_code(400);
	echo json_encode(['error' => 'Action inconnue']);
	exit;
}

if ($pid === null) {
	$command = '(exec python3 ' . escapeshellarg($scriptPath)
		. ') < /dev/null > /dev/null 2>&1 & echo $!';
	exec($command, $output, $exitCode);
	$pid = isset($output[0]) ? (int) $output[0] : 0;

	if ($exitCode !== 0 || $pid < 1) {
		http_response_code(500);
		echo json_encode(['error' => 'Le serveur Python n’a pas pu démarrer']);
		exit;
	}

	file_put_contents($pidFile, (string) $pid);
}

echo json_encode(['started' => true]);
?>