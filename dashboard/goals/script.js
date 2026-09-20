function complete_goal(goal_id,username,category,goal_type){
    toast('Goal complete.', 'success')
    document.location.href = "goal_completer.php?goal_id=" + goal_id + "&username=" + username + "&category=" + category + "&goal_type=" + goal_type
}

function uncomplete_goal(goal_id,username,category,goal_type){
    toast('Goal uncomplete.', 'success')
    document.location.href = "goal_uncompleter.php?goal_id=" + goal_id + "&username=" + username + "&category=" + category + "&goal_type=" + goal_type
}


function go_back(username){
    document.location.href = "/../dashboard/index.php?p=zi3di(ufe31ck433Klls&user=" + username
}