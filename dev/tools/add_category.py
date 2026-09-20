# imports
import psycopg2
import json


# load .env.json
with open("../../.env.json","r+") as file:
    raw_json = file.read()
    json_payload = json.loads(raw_json)


# get creds 
host = json_payload["host"]
port = json_payload["port"]
username = json_payload["username"]
password = json_payload["password"]
dbname = json_payload["dbname"]

# connect 
connect = psycopg2.connect(dbname=dbname,host=host,port=port,user=username,password=password)
cursor = connect.cursor()

# fetch user 
cursor.execute('select username from "user" order by id asc;')
users_list = cursor.fetchall()

# display user 
print("----------------------")

for user_id,user in enumerate(users_list):
    print(f" - {user_id+1} : {user[0]}")

print("----------------------\n")

# inputs 
user_input = int(input("Enter your user : "))
category_input = input("Enter your category : ")

goal1_input = input("1 : ")
goal2_input = input("2 : ")
goal3_input = input("3 : ")
goal4_input = input("4 : ")

# list goals
goals_list = []
goals_list.append(goal1_input)
goals_list.append(goal2_input)
goals_list.append(goal3_input)
goals_list.append(goal4_input)


# insert goal
for goal_id,goal in enumerate(goals_list):
    cursor.execute(f"insert into goal(nom,type) values('{goal}',{goal_id+1});")
    connect.commit()

# insert category
# get last goal id 
cursor.execute("select id from goal order by id desc limit 1;")
last_goal_id = cursor.fetchall()
last_goal_id = int(last_goal_id[0][0])


for i in range(4):
    cursor.execute(f"insert into category(user_id,goal_id,nom) values({user_input},{last_goal_id-i},'{category_input}');")
    connect.commit()

# close
cursor.close()
connect.close()