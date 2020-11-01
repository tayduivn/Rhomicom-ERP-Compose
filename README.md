# IN A CLOUD VM
1. Install Docker
2. Download ERP Project from Github
3. Run

docker-compose -p RHO-ERP-SET-1 up -d --remove-orphans 
docker-compose -p RHO-ERP-SET-1 down

4. Open http://localhost:8090 to import sample DB
5. Open http://localhost:8000 to run application