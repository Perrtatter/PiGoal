export function CreateGoBack(route, data = null) {
    let go_back_div = document.getElementById("go_back_div");

    if (!go_back_div) {
        console.error("L'élément #go_back_div est introuvable dans le DOM.");
        return;
    }

    console.log("creation de Go Back");
    
    // Si des données sont fournies, on utilise send_post au clic, sinon on garde le lien classique
    if (data) {
        go_back_div.innerHTML = `
            <button id='go_back' onclick='send_post("${route}", ${JSON.stringify(data)})'>
                <img id='Test' src='/assets/icons/go_back.svg'>
            </button>
        `;
    } else {
        go_back_div.innerHTML = `
            <a href="${route}">
                <button id='go_back'>
                    <img id='Test' src='/assets/icons/go_back.svg'>
                </button>
            </a>
        `;
    }
}
