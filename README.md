# ReHydrate Server

### Introduction
ReHydrate is a program that helps you to stay hydrated by creating a history, planning drink time on day and fire a notification when you need a drink =)
This server is divided into two parts: REST server and Web Application

### Technologies
1. PostgreSQL
1. PHP
1. JSON
1. JavaScript
   1. NodeJS
   1. Fetch
   1. Chart.js
   1. Notifications
1. REST
1. cURL
1. Bash
1. CSS
1. GNU GetText

### Scripts
The bash scripts provided are useful when you have to setup the environment to run this server.
##### setup_db.sh
This script creates the database using PostgreSQL, so you should just define your user and password. 

**Requirements:**
- PostgreSQL
- sudo

**Default behaviour:**
- Database name is `rehydrate`
- Database host is `localhost`
- Database schemas are stored into `setup.sql`

**Syntax:**

`./setup_db.sh USERNAME PASSWORD`

---
##### gen_locale.sh
This script generates localizzation files, those files are used only by webapp. 

**Requirements:**
- xgettext
- msgfmt

**Default behaviour:**
- Locale domain is `messages`
- Languages are `it_IT` and `en_US`
- Copyright about localizzation is mine

**Syntax:**

`./gen_locale.sh [SOURCES_PATH] [LOCALE_PATH]`

---
##### build_depends.sh

This script builds all third-party dependencies, those files are used only by webapp

**Requirements:**
- npm

**Default behaviour:**
- This script must be executed in place

**Syntax:**

`./build_depends.sh`

---
##### create_user.sh
This script create a new user into ReHydrate server, since it doesnt support online registrations. This script can be executed only after `setup_db.sh`.

**Requirements:**
- PostgreSQL
- sudo
- PHP

**Default behaviour:**
- Database name is `rehydrate`
- Database host is `localhost`
- Water daily target is fixed to `2500` mL

**Syntax:**

`./create_user.sh USERNAME PASSWORD`

### API
The backend interface is placed in endpoint `/rest.php`. All requests are via `POST`.

---
##### Auth
Before you can use all functions provided by ReHydrate server, you should authenticate with your username and password. 
It doesnt support online registrations, so you should have created an account through `create_user.sh`.
Notice that you are sending clear credential, so make sure you are on `localhost` OR you are on `HTTPS`.

**Parameters:**
- u: Username
- p: Password

**Return:**

- If authentication is successful, it will return HTTP CODE 200 and a **token** that is used by **ALL** next operations, to avoid sending every time your credentials.
- If authentication is failed, it will just return HTTP CODE 401.
- If there is a misconfiguration or bug into backend, it will just return HTTP CODE 500.

**Example:**

`curl -X POST -d 'u=test' -d 'p=1234' http://localhost:8000/rest.php`
> 07c546509aded72ee673cd43ee1018e60483670940b96a47bff6a0c08859ef01d1b4c22afbdb3e7351768cf08e98a9686d8fcfb7ea90e805bbe05f05b5e94b9b16f926157fce8376dd4e98f76c159cd60e5643641d697c25281cadba054e982200205796a68743d46c4897ff3a483b9b02391bc697f7a8259d3c89c56a6dd3272143a5d7ee5e6df1ca79e49c0d29e3e316e37bcf4c0962bdaed5c0fcb0fbcb8ab34a14e37d942fc259daac1d411f9f2a373c8ff6f648d6d1432d1f8b5d5565e2834df157793fda25d37dde69bc89007978fbefbd2e14108a2f367f6e5e915a8e2f8e543a5ae5d4d798690df8630d277a31ee573d1c6401f8a0e6dc20adebb604

---
##### Send
This action will provide to the server a new record that user drank an amount of water. 

**Parameters:**
- type: `send`
- quantity: Number of amount on mL of water drank 

**Return:**

- If successful, it will return HTTP CODE 200 and JSON with field status `OK`.
- If you didnt provide a valid token, it will just return HTTP CODE 401.
- If there is a misconfiguration or bug into backend, it will just return HTTP CODE 500 and JSON with field status `SQL_ERR`.

**Example:**

`curl -X POST -d 'token=07c546509aded72ee673cd43ee1018e60483670940b96a47bff6a0c08859ef01d1b4c22afbdb3e7351768cf08e98a9686d8fcfb7ea90e805bbe05f05b5e94b9b16f926157fce8376dd4e98f76c159cd60e5643641d697c25281cadba054e982200205796a68743d46c4897ff3a483b9b02391bc697f7a8259d3c89c56a6dd3272143a5d7ee5e6df1ca79e49c0d29e3e316e37bcf4c0962bdaed5c0fcb0fbcb8ab34a14e37d942fc259daac1d411f9f2a373c8ff6f648d6d1432d1f8b5d5565e2834df157793fda25d37dde69bc89007978fbefbd2e14108a2f367f6e5e915a8e2f8e543a5ae5d4d798690df8630d277a31ee573d1c6401f8a0e6dc20adebb604' -d 'type=send' -d 'quantity=100' http://localhost:8000/rest.php`
> {"status":"OK"}

---
##### Receive
This action will ask to the server a history about user drank. 

**Parameters:**
- type: `receive`
- sum: Aggregate data sum
  - `hourly`: Aggregate data by hour
  - `daily`: Aggregate data by day
  - `weekly`: Aggregate data by week
  - `monthly`: Aggregate data by month
  - `yearly`: Aggregate data by year
- time_start: Get history starting by an amount of time
  - `today`: Start by current day at 00:00
  - `24h`: Start by previous 24 hours
  - `week`: Start by current week
  - `month`: Start by current month
  - `year`: Start by current year
  - Any number is considered as unix timestamp
  - `0`: it will return all history
  - Any invalid input is considered as `0`
- time_end: get history previous this time
  - Any number is considered as unix timestamp
  - Any invalid input is considered as current time


**Return:**

- If successful, it will return HTTP CODE 200 and JSON:
  - status: `OK`
  - data: array
    - quantity: number of amount water drank
    - time: timestamp of when water was drank
- If you didnt provide a valid token, it will just return HTTP CODE 401.
- If there is a misconfiguration or bug into backend it will just return HTTP CODE 500 and JSON with field status `SQL_ERR`.

**Example:**

`curl -X POST -d 'token=07c546509aded72ee673cd43ee1018e60483670940b96a47bff6a0c08859ef01d1b4c22afbdb3e7351768cf08e98a9686d8fcfb7ea90e805bbe05f05b5e94b9b16f926157fce8376dd4e98f76c159cd60e5643641d697c25281cadba054e982200205796a68743d46c4897ff3a483b9b02391bc697f7a8259d3c89c56a6dd3272143a5d7ee5e6df1ca79e49c0d29e3e316e37bcf4c0962bdaed5c0fcb0fbcb8ab34a14e37d942fc259daac1d411f9f2a373c8ff6f648d6d1432d1f8b5d5565e2834df157793fda25d37dde69bc89007978fbefbd2e14108a2f367f6e5e915a8e2f8e543a5ae5d4d798690df8630d277a31ee573d1c6401f8a0e6dc20adebb604' -d 'type=receive' -d 'time_start=today' http://localhost:8000/rest.php`
> {"status":"OK","data":[{"quantity":"100","time":"1679236425"},{"quantity":"100","time":"1679236568"}]}


##### Receive plan
This action will ask to the server a plan of the day for user drink. 

**Parameters:**
- type: `receive`
- plan: `today`

**Return:**

- If successful, it will return HTTP CODE 200 and JSON:
  - status: `OK`
  - data:
    - array:
      - date: hour when user should drink water
      - time: amount of water to drink at that time
    - need: amount of mL of water need to drink today
- If you didnt provide a valid token, it will just return HTTP CODE 401.
- If there is a misconfiguration or bug into backend it will just return HTTP CODE 500 and JSON with field status `SQL_ERR`.

**Example:**

`curl -X POST -d 'token=07c546509aded72ee673cd43ee1018e60483670940b96a47bff6a0c08859ef01d1b4c22afbdb3e7351768cf08e98a9686d8fcfb7ea90e805bbe05f05b5e94b9b16f926157fce8376dd4e98f76c159cd60e5643641d697c25281cadba054e982200205796a68743d46c4897ff3a483b9b02391bc697f7a8259d3c89c56a6dd3272143a5d7ee5e6df1ca79e49c0d29e3e316e37bcf4c0962bdaed5c0fcb0fbcb8ab34a14e37d942fc259daac1d411f9f2a373c8ff6f648d6d1432d1f8b5d5565e2834df157793fda25d37dde69bc89007978fbefbd2e14108a2f367f6e5e915a8e2f8e543a5ae5d4d798690df8630d277a31ee573d1c6401f8a0e6dc20adebb604' -d 'type=receive' -d 'time_start=today' http://localhost:8000/rest.php`
> {"status":"OK","data":[{"date":"16:00","quantity":400},{"date":"18:00","quantity":400},{"date":"20:00","quantity":400},{"date":"22:00","quantity":400}],"need":"2300"}

