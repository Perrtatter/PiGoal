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
user_input = int(input("Enter your user : "))



# fetch category 
cursor.execute(f'select distinct nom from category where user_id = {user_input} order by nom asc;')
category_list = cursor.fetchall()

# display category 
print("----------------------")

for category_id,category in enumerate(category_list):
    print(f" - {category_id+1} : {category[0]}")

print("----------------------\n")
category_input = input("Enter your category : ")

'''
# get goal for category
cursor.execute(f"select * from category where nom='Gym' and user_id={users_list[user_id]} order by goal_id asc")
last_goal_id = cursor.fetchall()
last_goal_id = int(last_goal_id[0][0])

'''
'''
# insert goal
for goal_id,goal in enumerate(goals_list):
    cursor.execute(f"insert into goal(nom,type) values('{goal}',{goal_id+1});")
    connect.commit()

# remove category
# get last goal id 
cursor.execute("select id from goal order by id desc limit 1;")
last_goal_id = cursor.fetchall()
last_goal_id = int(last_goal_id[0][0])


for i in range(4):
    cursor.execute(f"insert into category(user_id,goal_id,nom) values({user_input},{last_goal_id-i},'{category_input}');")
    connect.commit()

'''

# close
cursor.close()
connect.close()