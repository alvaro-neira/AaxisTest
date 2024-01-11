# AaxisTest
Basic Technical Test PHP Symfony Developer

author: 
Alvaro Neira
alvaroneirareyes@gmail.com

## How to use it

NOTES:
* When using the REST API, you can use POST, GET or PUT with Postman, curl or similar.
* Assuming that PHP, PostgreSQL, symfony command are installed locally. This doesn't use Docker.

### 1. Install App
~~~bash
% git clone https://github.com/alvaro-neira/AaxisTest.git
~~~

Assume that the project is in ~/AaxisTest

~~~bash
% cd ~/AaxisTest
% composer install
% php bin/console doctrine:migrations:migrate
~~~

(if prompted, type "yes" or "y")

### 2. Get JWT token
Generate JWT key pair:
~~~bash
php bin/console lexik:jwt:generate-keypair
~~~

Run Server
~~~bash
symfony server:start
~~~

Register a user:
~~~
POST http://localhost:8000/api/registration
~~~

With body (JSON):
~~~json
{
    "email": "test@mail.com",
    "password": "pass4321"
}
~~~

It can be any email or password.

Now get the token:
~~~
POST http://localhost:8000/api/login_check
~~~

With body (JSON):
~~~json
{
    "username": "test@mail.com",
    "password": "pass4321"
}
~~~

Save the token of the response. It will be referred as JWT_TOKEN

### 3. Use the REST API

All the REST API calls have to set the header "Authorization: Bearer JWT_TOKEN"

#### 3.1 List of products. The endpoint simply brings a list of all the products with their data:
~~~
GET http://localhost:8000/api/products
~~~

#### 3.2 Load the records of the created entity. The endpoints will receive a JSON payload that may contain 1 or more records to load. In case of any error you must report it in the response:
~~~
POST http://localhost:8000/api/products
~~~

In the body you should input a JSON array with 1 or more products.
Example:
~~~json
[
{
    "sku": "123A",
    "product_name": "Shirt",
    "description": "ACME Shirt"
},
{
    "sku": "124B",
    "product_name": "Pants",
    "description": "ACME Pants"
},
{
    "sku": "125C",
    "product_name": "Shoes",
    "description": "ACME Pair of Shoes"
}
]
~~~

#### 3.3 Update of existing records. The endpoint will receive a JSON payload with a list that may contain 1 or more records to be modified. The product identification will be through the SKU field. In the response it must inform if it was updated correctly or in case of any error inform with which SKU it occurred:
~~~
PUT http://localhost:8000/api/products/{sku}
~~~

In the body you should input a JSON array with 1 or more products.
Example:
~~~json
[
{
    "sku": "123A",
    "product_name": "Shirt modified",
    "description": "ACME Shirt"
},
{
    "sku": "125C",
    "product_name": "Shoes",
    "description": "ACME Pair of Shoes modified"
}
]
~~~

It was tested with the following environment:
* MacBook Pro 2021 with M1
* Mac OS Sonoma 14.2.1
* PHP 8.1.27
* Symfony 6.4
* PostgreSQL 14
