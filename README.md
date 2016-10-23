# Webhook

For the project, I created two services:
- API: for inclusion, consultation and destination URL exclusion, and send a POST to a URL already registered.
- Driver: service that leads to POST queue, makes sending and control of attempts, which are no more than three at intervals of 30 minutes. In addition to delete the messages that have not been sent to more than 24 hours.


# How to use API:

1º) Add a new URL
PUT /destination 
JSON: {"url":"http://www.website.com/page-post"}

2º) Consult the URLs registered
GET /destination

3º) Remove a URL
DELETE /destination
JSON: {"id":"3"}

4º) Send a POST to a URL
POST /destination
JSON: {"id":"4", "msg-body": "BODY MESSAGE TEST", "content-type": "application/x-www-form-urlencoded"}

The API source code is in /url-api in the project directory.


# How to use Driver

The script must be run by CLI SAP on the server: $ php /driver/post.php
This script will be running and referring new POSTs in line all the time. If you need to scaling to sending, can generate other instances with the same script, there will be no competition because this possibility was treated in the system, creating a unique key in the log table in the database for the given POST. The instance that capture the POST and register in the log, concludes his submission, otherwise goes to the next.

The Driver source code is in /driver in the project directory.


# Rules for POST
- If the attempt to send the POST, the driver receives a different HTTP return of 200, will make a new attempt in 30 minutes;
- The driver will make a maximum of 3 attempts for each POST, if after the 3rd not receive the HTTP 200 return, POST is deleted;
- If an attempt to exceed 24 hours, the POST is deleted;
- The records of sent POST and attempts have not had successes are logged.


# Considerations:
- The API respects the REST-ful standard of 4 operations;
- You can scale the application, placing the API as webservers are required and especially the driver, which can be used in many instances to scale the sending POSTs;
- To withstand competition from multiple destinations, the service works in FIFO (first in first out);
- For safe operation, you may be adopted using HTTPS and authentication methods such as the use of a refresh token.