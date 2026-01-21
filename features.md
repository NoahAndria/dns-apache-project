# DNS + APACHE functionalities
Goal is to make a web interface of the configuration (creating, editing, etc) of DNS domains, and APACHE web pages.
Technology: php 
- there should be a Side bar that should contain links to: home (the first page), DNS, and APACHE
- first page should contain a choice for which of them will we use (so a choice between DNS, and APACHE)
## DNS
- When we go to the DNS page, it should have a list of all existing domains, and for each domain, there should be a delete button, and it should be clickable. And also an add domain button that will go to a page
- *add domain page: form for adding domain where there will be: a domain field with placeholder (ex: example.com), and an ip address field with placeholder (ex: x.x.x.x) and a validate domain button:
- #note: for test: let's use the ./etc/bind/ folder and create / edit the files inside it (like named.conf.local)
- #**feature** #1 add domain feature: 
    - appends this text to the named.conf.local file inside ./etc/bind

            zone "example.com" {
                type master;
                file './etc/bind/zones/db.example.com';
            }

    - and create the file db.example.com inside ./etc/bind/zones, and should already contain a default records list template

- And when we click this domain: it should go to the records list for this domain
- #**feature** list all records feature: 
- So page records list: should obviously contain all records, add record button (to another page*), edit (to another page also**) and delete button*** for each record
    - #**feature** add record feature
    - #**feature** edit record feature
    - #**feature** delete record feature
    - *page adding record: form with two columns: first column :record type (dropdown), name/host (placeholder: @, www, mail), TTL (dropdown default, second column (changes depending on record type) if A/AAAA : ip address; and so on
            And also a button to save or to cancel
    - **page editing record: just like the adding record page but the fields are pre-filled with the records of this record
    - ***Delete button should, when clicked, have a confirm alert or confirmation
- This page will also contain a test button: which will run the command sudo named-checkconf and sudo named-checkzones … and return the status (OK if everything is alright)
    - #**feature** check if ok feature
- Another page named: services where we can handle service status
- There should be a bind9 card, with three buttons: start, stop and restart
    Start: sudo systemctl start bind9
    Stop: sudo systemctl stop bind9
    Restart: sudo systemctl restart bind9
    - #**feature** start service feature
    - #**feature** stop service feature
    - #**feature** restart service feature
- in the domains list: there should be also a "dig" button that use the `dig` command
- #**feature** dig domain
 
 ## APACHE
- When we go to the Apache page, it should have 
Normal steps to create zone using bind9 (say example.com)
Adding files to /etc/bind/ and cd-ing to it ok
editing named.conf.local and adding the zone ok
Creating the file db.example.com ok
And adding the DNS records ok
Editing resolv.conf and change nameserver 127.0.0.1
Checking if nothing is wrong ok
Restarting bind9 ok
Digging it ok