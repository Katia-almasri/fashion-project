#! C:\Users\Dell\AppData\Local\Programs\Python\Python310\python.exe
#%%

from matplotlib.font_manager import json_dump
import numpy
import json
import mysql.connector
from pandas import read_csv
from pandas import to_datetime
from pandas import DataFrame
from prophet import Prophet
from matplotlib import pyplot
import pandas as pd
import sys
from datetime import date


####################### Read the passed arguement #####################
fileName = sys.argv[1]
# load data
path = 'storage\\csvFiles'
year = (str)(date.today().year)
csvFile = path+'\\'+(str)(year)+'_'+fileName+'.csv'
print(csvFile)
df = pd.read_csv(csvFile, header=0)


df.columns = ['y', 'ds']
df['ds']= to_datetime(df['ds'])
df['cap'] = 8.5
df['floor'] = 1.5

model = Prophet(growth='logistic')
model.fit(df)

# future = model.make_future_dataframe(periods=12 , freq='MS')
# future['cap'] = 8.5
# future['floor'] = 0
# forecast = model.predict(future)

# fit the model
#model.fit(df)
##################### Define the current year ###########################
currentYear = date.today().year
dateFormat = str(currentYear)+'-%02d'
#define the future predicted list
future = list()
for i in range(1, 13, 2):
	date = dateFormat % i
	future.append([date])
 
future = DataFrame(future)
future.columns = ['ds']
future['ds']= to_datetime(future['ds'])

future['cap'] = 0.1
future['floor'] = 0
forecast = model.predict(future)
# model.plot(forecast)
# pyplot.show()
#print(forecast[['ds', 'yhat', 'yhat_lower', 'yhat_upper']].head(6))
try:
  mydb = mysql.connector.connect(
    host="localhost",
    user="root",
    password="123456",
    database="fashion"
  )
  mycursor = mydb.cursor()

  dataFrameToList = list(forecast[['ds', 'yhat', 'yhat_lower', 'yhat_upper']].itertuples(index=False))
  sql = "INSERT INTO predicted (ds, yhat, yhat_lower, yhat_upper) VALUES (%s, %s, %s, %s)"
  mycursor.executemany(sql, dataFrameToList)
  mydb.commit()
  print("predictions has been inserted successfully")
  mycursor.close()
  
except mysql.connector.Error as error:
    print("Failed to insert record into Laptop table {}".format(error))
    
finally:
    if mydb.is_connected():
        mydb.close()

 







# %%
