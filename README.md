# TomatoCart_Tax-Localities
TomatoCart lacks the ability to apply sales tax in states with variable tax rates in localities. This will allow tax percentages by ZIP and state. (USA)

[Based on TomatoCart V1.1.8.4]

Project begins: 4/9/2018

The current setup in TomatoCart allows us to add Countries and their entities -- in this case, states. We can then create Zones, and tax groups. However, there is no application where taxes are applied differently by town/city/county/or ZIP. Many states in USA have locality taxes where additional taxes are added by city or local municipality above and beyond the set state tax.

I.E. : (This is purely an example, and the numbers are meaningless)

    Oregon State Tax: 6.5%
    Sherwood, Oregon: 1.8%
    -----------------------
          TOTAL TAX : 8.3%
          
    (0.065 + 0.018 = 0.083)
   
   
The module will need to be able to accept city listings and/or ZIP code listings, depending on how the state defines tax divisions upon municipalities. I will be taking the existing code and breaking it down to determine how TomatoCart assembles taxes and applies them during transactions.

A module will then need to be programmed to be accepted by the admin console so we can use the admin mode to add, delete, and arrange localities at will using the qWikiOffice interface.

Programming will then need to be done so we can inject the taxes into the cart during transactions.

# Admin Module
4/22/2018

Work on the Admin module for the State Tax plug-in is complete. All functions have been tested and the code has been added to the Github repository.

Other files may have been changed, the plug-in will need to get used and we will ammend and/or add files as comments come in, requests are made, or code is submitted by users. 

Once all code is complete - Admin and Client side - my intention is to create an easy-to-use installer for the module so users don't need to hunt around their directories for the right places to put files.

# Client Usage
4/30/2018

Uploaded changed Tomatocart Files to make new tax method work in order totalling. See Notes.txt for further information.
