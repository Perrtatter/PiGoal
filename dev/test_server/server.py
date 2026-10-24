import asyncio
import websockets
import time
from websockets.exceptions import ConnectionClosedError, ConnectionClosedOK

t = 0
async def send_data(websocket):
    global t
    print("Client connecté au WebSocket !")
    try:
        while True:
            message = f"Message n°{t} envoyé à {time.strftime('%H:%M:%S')}"

            await websocket.send(message)
            t += 1
            await asyncio.sleep(1)
            
    except ConnectionClosedOK:
        print("Le client s'est déconnecté proprement.")
    except ConnectionClosedError:
        print("Connexion perdue avec le client.")

async def main():
    async with websockets.serve(send_data, "localhost", 8765):
        print("Serveur WebSocket démarré sur ws://localhost:8765")
        await asyncio.Future()

if __name__ == "__main__":
    asyncio.run(main())
