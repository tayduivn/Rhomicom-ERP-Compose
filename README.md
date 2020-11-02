# TO SETUP RHOMICOM ERP PROJECT
1. Install Docker
2. Download ERP Project from Github
- NB on linux you may have to run 
- chmod -R 777 Rhomicom-ERP-Project as root
3. cd  to api_image/code and run
- npm install
4. cd to db/initdb/pgdb and download sample db file into it if needed
- NB: Sample DB Can be downloaded from 
https://gitlab.rhomicom.com:8443/admin2/rhomicom-public-stuff/-/blob/master/2_sample_database.sql
5. cd back to main Rhomicom-ERP-Project folder and Run
- docker-compose -p RHO-ERP-SET-1 up -d --remove-orphans  
6. Open http://localhost:8090 to access db via adminer
7. Open http://localhost:8091 to access db via pgadmin
8. Open http://localhost:8000 to run application
9. To tearn everything down Run  
- docker-compose -p RHO-ERP-SET-1 down  

# TO BUILD IMAGES
1. Download psol.tar.gz file from https://github.com/openbridge/nginx/tree/master/psol and place in conf/psol/psol.tar.gz
2. Navigate to Dockerfile Folder 
3. In the api_image folder Run 
- docker build -t rho-erp-api:v1.0 .
- docker run -p 3000:3000 --name rho-erp-api-1 rho-erp-api:v1.0
4. In the api_image folder Run 
- docker build -t rho-erp-base:v1.0 .
- docker run -dp 8000:8080 --name rho-erp-base-1 rho-erp-base:v1.0

# Rhomicom-ERP-Web
Rhomicom Enterprise Resource Planning System (Web Version)

Rhomicom Enterprise Resource Planning System is a complete ERP system proudly started in Ghana by Ghanaians.
The company behind this project is Rhomicom Systems Tech. Ltd.(http://rhomicom.com)
The software can be used to manage both backend and front-end operations of all kinds of institutions and organisations.
The sample is currently hosted on https://portal.rhomicom.com
Help can be found on https://wiki.rhomicom.com

Completed or Working Modules
=======================================
1. Accounting
2. Person Data
3. Internal Payments (Payroll, Membership Fees, Dues and Contributions)
4. Sales/Inventory
5. Visits/Appointments
6. Events Management
7. Facility Rentals/Hospitality Management
8. Learning/Performance Management System
9. Self-Service
10. e-Voting
11. Chat Rooms/Forums
12. Banking & Microfinance Module
13. Vault Management Module
14. Visit https://wiki.rhomicom.com/ for more info

Typical Target institutions/organisations
=========================================
1. Hotels/Restaurants/Coffee shops
2. Super Markets/Boutiques/Wholesale and Retail Shops
3. Associations/Churches/Professional Bodies
4. Construction Firms
5. Academic Institutions

Sofware Requirements
=======================
1. All Operating Systems
2. PostgreSQL Database 9.3 or later (Can be hosted on any Operating System)
3. Java 1.6 or later

Hardware Requirements
=====================
Same hardware requirements for above Sofware requirements

Technologies/Tools Used 
==================================================
1. Java 1.8-(Netbeans 8.1)
2. PHP 5.6 or later
3. HTML/CSS/javascript (Bootstrap3.3.6, Datatables Plugin, Bootstrap Dialog, SummerNote,jQuery 1.11.3, fontawesome 4)
4. TIBCO JasperSoft Studio 6.2.0
5. PostgreSQL Database 9.3

# (CREDITS/ACKNOWLEDGEMENTS)
- **Platforms:** Gitlab/Github, Docker, Alpine Linux, Nginx, ModPageSpeed
- **Languages:** Java, PHP, Bootstrap, jQuery, Node, ExpressJS,
- **Database:** PostgreSQL, MySQL, Adminer
- **IDE:** vscode
- **Openbridge, Inc.:** https://github.com/openbridge/nginx

# COMMERCIAL SUPPORT AVAILABLE
- **URL:** ...coming soon...
- **Contact:** support@rhomicom.com


# TODO
1. Projects Management Module
2. Help Desk Module

# Issues

If you have any problems with or questions about this image, please contact us through a GitHub issue.

# Contributing

You are invited to contribute new features, fixes, or updates, large or small; we are always thrilled to receive pull requests, and do our best to process them as fast as we can.

Before you start to code, we recommend discussing your plans through a GitHub issue, especially for more ambitious contributions. This gives other contributors a chance to point you in the right direction, give you feedback on your design, and help you find out if someone else is working on the same thing.

# License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
