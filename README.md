# rss-feed

#Requirements:
  node & npm
  docker & docker-compose
  
#Set Up:

cd docker
./start
cd ../angular && npm i && npm run start
go to localhost:4200

#Tear Down:
ctrl + C for angular live server
cd ./docker
./stop
