export function CreateGoBack(route) {
    let go_back_div = document.getElementById("go_back_div");

    // Sécurité : on vérifie si l'élément existe bien dans la page
    if (!go_back_div) {
        console.error("L'élément #go_back_div est introuvable dans le DOM.");
        return;
    }

    console.log("creation de Go Back");
    go_back_div.innerHTML = `
        <a href="${route}"><button id='go_back'>
            <img id='Test' src='/assets/icons/go_back.svg'>
        </button></a>
    `;
}
