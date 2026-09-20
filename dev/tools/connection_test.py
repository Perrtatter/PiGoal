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