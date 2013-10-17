SugarES
=======

Elasticsearch Helper for SugarCRM

INSTALL
=======
Download or Clone contents from GitHub into WebServer.

$ git clone https://github.com/meveridge/SugarES

Update via git pull:

$ cd SugarES/
$ git pull

TREE
=======
Shows the current hierarchy of the index. Expand out to see which modules have been indexed and which modules have data. Each module will only expand to the first 50 FTS results from that module. Click on a record ID to view the record in the main panel.

INJECT
=======
Allows you to create a new record in the FTS server. 

SEARCH
=======
Performs a basic search on the server for criteria. The search is an exact match so you should include wildcards (*) when necessary. Words separated by a space will perform the OR condition by default, you may specify an AND manually. To search by an Id you must first select a type from the dropdown. When specifying an Id the Query entry is ignored.
