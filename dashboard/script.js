function go2category(category,username){
    // encode for url 
    category = encodeURI(category)
    username = encodeURI(username)

    // redirect
    document.location.href = "goals/index.php?username=" + username + "&category=" + category
}