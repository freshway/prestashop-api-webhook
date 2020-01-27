# WebHook Prestashop Module

This module enable sending a JSON payload containing a fresh validated order to a POST URL.

I needed to get information when a product was updated or removed, so I took this repository as a base, I don't know prestashop very well, so I made it as generic as possible, you can add more action hooks

You can see a list here

https://devdocs.prestashop.com/1.7/modules/concepts/hooks/list-of-hooks/


# How does it work?

Gets an event when a product is added, modified or delimited.

save the event in a queue in the database and with a Job Cron Send the data to the endpoint.

enjoy it!
