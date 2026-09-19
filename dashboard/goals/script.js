function complete_goal(goal_id,username,category){
    toast('Goal complete.', 'success')
    document.location.href = "http://localhost:3000/dashboard/goals/goal_completer.php?goal_id=" + goal_id + "&username=" + username + "&category=" + category
}


function go_back(username){
    document.location.href = "http://localhost:3000/dashboard/index.php?p=zi3di(ufe31ck433Klls&user=" + username
}