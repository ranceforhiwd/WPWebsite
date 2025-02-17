import boto3
import json 
import time 
import requests from googlesearch 
import search from bs4 
import BeautifulSoup 
import re 
import pgeocode from appfunctions 
import * from boto3.dynamodb.conditions 
import Key, Attr 
import random

output = {}
arr = []
address = []
phones = []
zipcodes = []
states =[]
emails = []
cities = []
website = []
longitude = []
latitude = []
city = []
time_created = time.time()
mytime = str(time_created)
what = 'Medical Supplies near'
h = 'Hospitals near'
state = 'GA'
nomi = pgeocode.Nominatim('us')
vocabulary = ['pkwy','ave','blvd','road','street','avenue','hwy','route','way']
country = "USA"
random_int = random.randint(0, 99)
df = pd.read_excel("georgia.xls")
row = df.iloc[[random_int]]
row_city = row.get('City')

for rc in row_city:
   city.append(rc)

print(city)
print('-------')
sk = search(h+" "+city[0]+" "+state+" "+country, num_results=1)
print(sk)
#exit()
for s in sk:
   if s.startswith("https:") or s.startswith("http:"):
      print(s)
      website.append(s)
      r = requests.get(s)
      soup = BeautifulSoup(r.content.strip(), 'html.parser')
      webtext = soup.get_text().splitlines()

      #search text
      for w in webtext:
         if w != '':
            #using vocabulary for possible addresses
            checkvocb = isvocb(w.lower(), vocabulary)
            if checkvocb:
               for j in checkvocb:
                  address.append(j)
            emails.append(emailadx(w.lower()))
            phones.append(getphones(w.lower()))

            wl = w.split()
            for l in wl:
               b = l.replace("," , "")
               if contains_numbers(b):
                  postal = nomi.query_postal_code(b)
                  if postal.accuracy > 2:
                     cities.append(postal.place_name)
                     zipcodes.append(postal.postal_code)
                     print(postal.postal_code)
                     states.append(postal.state_code)
                     longitude.append(str(postal.longitude))
                     latitude.append(str(postal.latitude))
      output['address'] = list(dict.fromkeys(address))
      output['states'] = list(dict.fromkeys(states))
      output['zipcodes'] = list(dict.fromkeys(zipcodes))
      output['phones'] = list(dict.fromkeys(phones))
      output['emails'] = list(dict.fromkeys(emails))
      output['longitude'] = list(dict.fromkeys(longitude))
      output['latitude'] = list(dict.fromkeys(latitude))
      output['website'] = website

      if zipcodes:
         output['cities'] = list(dict.fromkeys(cities))
      else:
         output['cities'] = city

      #save to db
      session = createSession(boto3)
      tableName = 'markup'
      dbName = 'scan_pdf_content2'
      saveResultsInMarkupTable(session, output, mytime, dbName, tableName, mytime)
      print("Record saved")
   else:
      print("no match")


