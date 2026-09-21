
function createCategory(){
    let menu_container = document.getElementById("menu-container")

    menu_container.innerHTML = `
    <div id="create-category-menu"></div>
    <div id="menu">
        <div class="header"><span style="margin-left: 90%;"><button onclick="closeMenu()" id="close-button">X</button>Creation d'un categorie</span></div>
        <div class="content">
            <div style="margin-bottom:10px;position:relative;top:4vw;">
                <p>Nom de la categorie : </p>
                <input type="text" placeholder="Nom de la categorie" style="width: 76%;" id="new_categorie_name"></input>
            </div>
            <button class="add_cat_btn" style="width: 53vw;font-weight: 500;margin-top: 7vw;" onclick="addCategory()">Ajouter la categorie</button>
            <p style="margin-top: -1vw;color:red;" id="erreur_p"></p>
        </div>
    </div>`
}

function addCategory(){
    let new_categorie_name = document.getElementById("new_categorie_name").value
    let erreur_p = document.getElementById("erreur_p")
    if(new_categorie_name.trim()==''){erreur_p.innerText="Veuiller Remplir les champs"}
    else{
        console.log("test Create : " + new_categorie_name)
        addCategoryToDB(new_categorie_name)
        closeMenu()
    }
}

function closeMenu(){
    let menu_container = document.getElementById("menu-container")
     menu_container.innerHTML = ``
}

async function addCategoryToDB(name) {
    const response = await fetch("addCategory.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ name })
    });

    const text = await response.text();

    console.log("Réponse PHP :", text);
}

function del_category(){
    toast("Not aviable yet.","info")
}