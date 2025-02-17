import json
import time
import re
from boto3.dynamodb.conditions import Key, Attr
import numpy as np
import pandas as pd

def createSession(boto3):
   #create session with credentials
   sessionParam = boto3.Session(
       region_name = 'us-east-1'
   )
   return sessionParam

def getRecordFromTable(session, dbName, fileName ):
   # Get the service resource.
   dynamodb = session.resource('dynamodb')
   # dbName = scan_pdf_content2
   table = dynamodb.Table(dbName)
   #set the appraisal table
   #appraisalTbl = dynamodb.Table(tblName)
   
   response = table.query(
            IndexName='pk-pngfilename-index',
            KeyConditionExpression=Key('pk').eq('churchillrealestate') & Key('pngfilename').eq(fileName)
   )
    
   return response['Items']


def saveResultsInMarkupTable(session,data,fileName,dbName, tblName,pngFile):
   dynamodb = session.resource('dynamodb')
   table = dynamodb.Table(dbName)
   print(data)
   #set the appraisal table
   appraisalTbl = dynamodb.Table(tblName)
   if len(data) > 0:
      #save item to db
      appraisalTbl.put_item(
            Item={
                    'pngname_pk': 'prospects',
                    'pngname_sk': 'time_processed_'+pngFile,
                    'png_file_name': pngFile,
                    'pdf_file_name': fileName,
                    'json_data': data
                }
            )
      print('Data save to DB!')
   else: 
      print('Error: DB Insertion Failed!')

def isvocb(n, vocb):
   adx = []
   for v in vocb:
      x = n.replace(",","").find(v)
      if x != -1:
         y = n.split()
         if y.count(v) > 0:
            z = y.index(v)
            for j in range(3, 0, -1):
               adx.append(y[z-j])
            return adx

def getphones(n):
   reg = re.compile(".*?(\(?\d{3}\D{0,3}\d{3}\D{0,3}\d{4}).*?", re.S)
   results = reg.findall(n)
   if results != []:
      r = results.pop()
      return r

def emailadx(n):
   if "@" in n:
      return n
      #words = n.split(" ")
      #for w in words:
         #if "@" in w:
            #a = w.split(".com")
            #return a[0]+".com"

def contains_numbers(input_str):
    if len(input_str) >= 5 and len(input_str) <=10 and input_str.isdigit():
       return bool(re.search(r'\d', input_str))
    else:
       return False
