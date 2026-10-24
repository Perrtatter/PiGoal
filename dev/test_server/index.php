<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[DEV] Test Server</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body align="center">
    <h1>[DEV] Test Server</h1>
    <hr>
    <p id="status">🔴 Server pas connecté</p>
    <p id="data-display"></p>
    <?php
        echo "<button onclick='lunch_server()' class='button'>Lancer le serveur</button> ";
        echo "<button onclick='stop_server()' class='button'>Arrêter le serveur</button>";
    ?>
</body>
</html>

<script>
    const launchEndpoint = <?php echo json_encode(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/lunch_server.php'); ?>;
    let activeSocket = null;
    let stopping = false;

    async function lunch_server() {
        const statusElement = document.getElementById('status');
        const displayElement = document.getElementById('data-display');

        stopping = false;
        statusElement.innerText = "🟠 Démarrage du serveur...";
        displayElement.innerText = "";

        try {
            const response = await fetch(launchEndpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ action: 'start' })
            });
            if (!response.ok) {
                throw new Error('Le serveur PHP n’a pas pu démarrer le processus.');
            }
        } catch (error) {
            statusElement.innerText = "🔴 Impossible de lancer le serveur";
            displayElement.innerText = error.message;
            return;
        }

        let attempts = 0;
        const connect = () => {
            activeSocket = new WebSocket('ws://localhost:8765');

            activeSocket.onopen = function() {
                statusElement.innerText = "🟢 Serveur connecté";
                displayElement.innerText = "";
            };

            activeSocket.onmessage = function(event) {
                displayElement.innerText = event.data;
            };

            activeSocket.onerror = function() {
                activeSocket.close();
            };

            activeSocket.onclose = function() {
                activeSocket = null;
                if (stopping) {
                    return;
                }
                if (attempts < 10) {
                    attempts += 1;
                    statusElement.innerText = "🟠 Connexion au serveur...";
                    window.setTimeout(connect, 500);
                    return;
                }

                statusElement.innerText = "🔴 Serveur WebSocket indisponible";
                displayElement.innerText = "Le serveur ne répond pas sur le port 8765.";
            };
        };

        connect();
    }

    async function stop_server() {
        const statusElement = document.getElementById('status');
        const displayElement = document.getElementById('data-display');

        stopping = true;
        if (activeSocket) {
            activeSocket.onclose = null;
            activeSocket.close();
            activeSocket = null;
        }

        try {
            const response = await fetch(launchEndpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ action: 'stop' })
            });
            if (!response.ok) {
                throw new Error('Le serveur PHP n’a pas pu arrêter le processus.');
            }
            statusElement.innerText = "🔴 Serveur arrêté";
            displayElement.innerText = "Compteur remis à zéro pour le prochain lancement.";
        } catch (error) {
            statusElement.innerText = "🔴 Erreur à l’arrêt du serveur";
            displayElement.innerText = error.message;
        }
    }

    window.addEventListener('pagehide', function() {
        stopping = true;
        if (activeSocket) {
            activeSocket.onclose = null;
            activeSocket.close();
            activeSocket = null;
        }
        navigator.sendBeacon(launchEndpoint, new URLSearchParams({ action: 'stop' }));
    });
</script>