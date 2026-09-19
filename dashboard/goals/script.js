function complete_goal(goal_id,username,category,goal_type){
    toast('Goal complete.', 'success')
    document.location.href = "http://localhost:3000/dashboard/goals/goal_completer.php?goal_id=" + goal_id + "&username=" + username + "&category=" + category + "&goal_type=" + goal_type
}


function go_back(username){
    document.location.href = "http://localhost:3000/dashboard/index.php?p=zi3di(ufe31ck433Klls&user=" + username
}