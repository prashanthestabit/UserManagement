# Introduction

The User Management module is a pre-built and maintained module that provides all the necessary functionality for user manangement in a Laravel project. The module includes features such as user create, users list, fetch user by id, udpate user information, delete user, fetch roles assigned to user, assign roles to user and remove role assign to user. By using this module, developers can save time and effort in implementing these common user management features in their projects, while promoting consistency and standardization in module design and implementation.for this module we are using the JWT Authentication 


# Requirement

1. [Laravel freamwork](https://laravel.com/) 
2. [nWidart/laravel-modules package](https://nwidart.com/laravel-modules/v6/installation-and-setup)
3. [JWT authentication](https://jwt-auth.readthedocs.io/en/develop/)


## Steps to use this module


#### Step 1:- Install Module Package Libraray

```bash
 composer require nwidart/laravel-modules
```
 Step 1.1: Create Modules folder on root laravel project also register in composer.json

``` bash
    {
    "autoload": {
        "psr-4": {
        "App\\": "app/",
        "Modules\\": "Modules/"
        }
    }
    }
```
<b>Tip:</b> don't forget to run <b>composer dump-autoload</b> afterwards

##### Step 1.2: clone the code in Modules folder

if don't have Modules folder on laravel root then create manually.

``` bash
git clone https://github.com/Hestabit/UserManagement
```
<b>Tip:</b> don't forget to run <b>php artisan module:enable UserManagement</b> afterwards

Step 2:- Run php artisan migrate

## Features

1) [User List](#1-userlist)
2) [Create User](#2-createuser)
3) [Fecth User Details By Id](#3-userdetails)
4) [Update User](#4-updateuser)
5) [Delete User](#5-deleteuser)
6) [Fetch The Roles Assign To User](#6-fetchtherolesassigntouser)
7) [Assign Role To User](#7-assignrole)
8) [Audit Logs](#8-auditlogs)


## EndPoints


#### 1. UserList

```bash
URL:- /api/users

Method:- GET
```

Request Body:- 

|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     token           |     string         |       Yes           |      JWT Token                 |


#### 2. CreateUser

```bash
URL:- /api/users/store

Method:- POST
```

Request Body:-

|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     name            |     string         |       Yes           |       Name of the user         |
|     email           |     email          |       Yes           |       Email of the user        |
|    password         |     string         |       Yes           |       Password of the user     |
|password_confirmation|     string         |       Yes           |       Confirm Pasword          |
|     token           |     string         |       Yes           |       JWT Token                |


#### 3. UserDetails

```bash
URL:- /api/users/{id}

Method:- GET
```
Request Body:- 

|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     token           |     string         |       Yes           |      JWT Token                 |


#### 4. UpdateUser

```bash
URL:- /api/users/update

Method:- PUT
```
Request Body:- 

|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     token           |     string         |       Yes           |      JWT Token                 |
|     name            |     string         |       Yes           |       Name of the user         |
|     email           |     email          |       Yes           |       Email of the user        |


#### 5. DeleteUser

```bash
URL:- /api/users/{id}
Method:- DELETE
```
Request Body:- 

|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     token           |     string         |       Yes           |      JWT Token                 |


#### 6. FetchTheRolesAssignToUser

```bash
URL:- /api/user/{id}/roles

Method:- GET
```
Request Body:- 

|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     token           |     string         |       Yes           |      JWT Token                 |

#### 7. AssignRole

```bash
URL:- /api/user/{id}/role
Method:- POST
```
Request Body:- 

|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     token           |     string         |       Yes           |      JWT Token                 |
|     role_id         |     integer        |       Yes           |      Role Id                   |


#### 8. DeleteRole

```bash
URL:- /api/user/{id}/role/{role_id}
Method:- DELETE
```
|    Parameter        |     Type           |     Required        |          Description           |
|:-------------------:|:------------------:|:-------------------:|:------------------------------:|
|     token           |     string         |       Yes           |      JWT Token                 |

## NOTE:- For testing the api you can run the following command


```bash
php artisan test Modules/UserManagement/Tests/Unit/UsersControllerTest.php
```
