#!/usr/bin/python3

#
# (C) STEM Loyola 2021. Part of Project #04
#
# This script:
#    (1) Connects to the file-parser server and fetch for its data
#    (2) Converts the data to JSON
#    (3) Connects to demos.stemloyola.org and upload the data

import json
import requests    # TODO: Install any missing package. In Thonny: Tools > Manage Packages...
import socket
from pprint import pprint


HOST_IP = "localhost"  # Or "127.0.0.1"
HOST_PORT = 9090       # Must match the port a C++ server is listening on
BUFF_SIZE = 4096       # Fetch socket data 4 KB at a time

# UPLOAD_URL = "https://demos.stemloyola.org/coder/fsowani/api/upload-data.php"
UPLOAD_URL = "https://demos.stemloyola.org/coder/amrema/api/upload-data.php"    # TODO: Add your custom URL


#
# Fetch data from the socket defined by the file-parser server (C++ program)
# Resource: Python networking: https://www.tutorialspoint.com/python3/python_networking.htm
#
def fetchData(host, port):
    data = None
    
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.connect((HOST_IP, HOST_PORT))
        sock.sendall(b'/')
        
        # Receive all data sent by the file parser
        data = b''
        while True:
            part = sock.recv(BUFF_SIZE)
            data += part
            if len(part) < BUFF_SIZE: # Stop when 0 or end of data
                break
            
    except Exception as e:
        print("[ERROR] Fetching data: {}".format(e))
        data = None
        
    finally:
        return data.decode("utf-8")  # Return data as string


#
# Convert CSV data to JSON
# JSON Tutorial: https://beginnersbook.com/2015/04/json-tutorial
#
def buildJSON(strData):  
    listData = strData.split("\n")  # Each city's data is on a seperate line
    header = listData[0].split(",")  # First line contains data headers
    
    cities = []
    
    for row in listData[1:]:   # Actual data is on second row to the end
        listRow = row.split(",")   # City's data elements are comma-seperated
        
        # Populate city data from current row and add to cities list
        city = {}
        for i in range(len(header)):
            
            # Check if the population key string is not having '\r' at the end
            if '\r' in header[i]:
                index = header[i].find('\r')
                header[i] = header[i][0:index]
                
            city[header[i]] = listRow[i]
            
        cities.append(city)

    jsonData = { "data": cities }
    
    return jsonData


#
# Sends data to your site in the demos server (UPLOAD_URL)
# Resources: - https://www.w3schools.com/python/ref_requests_response.asp
#
def uploadData(data):
    try:
        response = requests.post(UPLOAD_URL, json=data)
        pprint(response.text)
        
        if response.ok:
            respData = response.json()   # Use "response.text" to get contents as text
            
            if respData["status"] == "success":
                return True
            else:
                print("[ERROR] Uploading data: {}".format(respData["message"]))
        else:
            print("[ERROR] Uploading data: {}".format(response.reason))
    except Exception as e:
        print("[ERROR] {}".format(e))
    
    return False

#
# Main
#
if __name__ == "__main__":
    print("Fetching data...")
    data = fetchData(HOST_IP, HOST_PORT)
    pprint(data)
    
    if data == None:
        print("Is the file-parser running?")
        exit(1)
        
    print("Reformatting data for upload...")
    jsonData = buildJSON(data)
    pprint(jsonData)
    
    print("Uploading data...")
    if uploadData(jsonData):
        print("Uploaded successfully")
    else:
        print("Are you connected to the Internet?")
    
