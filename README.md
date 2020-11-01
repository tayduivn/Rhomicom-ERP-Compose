# TO SETUP RHOMICOM ERP PROJECT
1. Install Docker
2. Download ERP Project from Github
3. Run
- docker-compose -p RHO-ERP-SET-1 up -d --remove-orphans  
4. Open http://localhost:8090 to import sample DB
- NB: Sample DB Can be downloaded from 
https://gitlab.rhomicom.com:8443/admin2/rhomicom-public-stuff/-/blob/master/2_sample_database.sql
5. Open http://localhost:8000 to run application
6. To tearn everything down Run  
- docker-compose -p RHO-ERP-SET-1 down  

# TO BUILD IMAGES
1. Download psol.tar.gz file from ...coming soon... and place in conf/psol/psol.tar.gz
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

Various Modules being worked on are
=======================================
1. Accounting
2. Person Data
3. Internal Payments (Payroll, Membership Fees, Dues and Contributions)
4. Sales/Inventory
5. Visits/Appointments
6. Events Management
7. Facility Rentals/Hospitality Management
8. Projects Management
9. Learning/Performance Management System
10. Self-Service
11. e-Voting
12. Chat Rooms/Forums

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
- **Openbridge Inc:** https://github.com/openbridge/nginx

# COMMERCIAL SUPPORT AVAILABLE
- **URL:** ...coming soon...
- **Contact:** support@rhomicom.com


# TODO


# Issues

If you have any problems with or questions about this image, please contact us through a GitHub issue.

# Contributing

You are invited to contribute new features, fixes, or updates, large or small; we are always thrilled to receive pull requests, and do our best to process them as fast as we can.

Before you start to code, we recommend discussing your plans through a GitHub issue, especially for more ambitious contributions. This gives other contributors a chance to point you in the right direction, give you feedback on your design, and help you find out if someone else is working on the same thing.

# License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
