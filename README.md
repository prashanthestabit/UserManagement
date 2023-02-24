# Introduction

The User Management module is a pre-built and maintained module that provides all the necessary functionality for user manangement in a Laravel project. The module includes features such as user create, users list, fetch user by id, udpate user information, delete user, fetch roles assigned to user, assign roles to user and remove role assign to user. By using this module, developers can save time and effort in implementing these common user management features in their projects, while promoting consistency and standardization in module design and implementation.


# Requirement

Laravel freamwork -nWidart/laravel-modules package, php 7.2 or higher

# Tip
For this User Management Module we are using the JWT Authentication 

## Steps to use this module


Step 1:- Install Module Package Libraray


```bash
composer require nwidart/laravel-modules
```

Step 2:- Run php artisan migrate



## EndPoints


1) Fecth User List

```bash
**URL:-** **/api/users**
Method:- GET
Request Body:- token (required)
Response:- 
1.1) If Success: HTTP_OK response code :- 200 with JSON containing users list.
1.2) If Unsuccess: HTTP_BAD_REQUEST response code :- 400 Bad Request (or) 
HTTP_INTERNAL_SERVER_ERROR response code:- 500 Internal Server Error with error
message in JSON Format.
```

2) Create User

```bash
URL:- http://127.0.0.1/api/users/store
Method:- POST
Request Body:- token (required), name(required,string,max:255), email(required,email,unique),
password (required,string,min:6.max:50,confirmation), password_confirmation (same as password).
Response:- 
2.1) If Success: HTTP_OK response code :- 200 send success message in JSON Format.
2.2) If Unsuccess: HTTP_BAD_REQUEST response code :- 400 Bad Request (or) 
HTTP_INTERNAL_SERVER_ERROR response code:- 500 Internal Server Error with error
message in JSON Format.
```


3) Fecth User Details By Id

```bash
URL:- http://127.0.0.1/api/users/{id}
Method:- GET
Request Body:- token (required)
Response:- 
3.1) If Success: HTTP_OK response code :- 200 send success message with user information
in JSON Format.
3.2) If Unsuccess: HTTP_BAD_REQUEST response code :- 400 Bad Request (or) 
HTTP_INTERNAL_SERVER_ERROR response code:- 500 Internal Server Error with error 
message in JSON Format.
```


4) Update User Details

```bash
URL:- http://127.0.0.1/api/users/update
Method:- PUT
Request Body:- token (required), name(string,max:255), email(email,unique)
Response:- 
4.1) If Success: HTTP_OK response code :- 200 update user details and send success 
message with user udpated information in JSON Format.
4.2) If Unsuccess: HTTP_BAD_REQUEST response code :- 400 Bad Request (or) 
HTTP_INTERNAL_SERVER_ERROR response code:- 500 Internal Server Error with error
message in JSON Format.
```


5) Delete User

```bash
URL:- http://127.0.0.1/api/users/{id}
Method:- DELETE
Request Body:- token (required)
Response:- 
5.1) If Success: HTTP_OK response code :- 200 delete user data and send success message
in JSON Format.
5.2) If Unsuccess: HTTP_INTERNAL_SERVER_ERROR response code:- 500 Internal Server
Error with error message in JSON Format.
```


6) Fetch The Roles Assign To User

```bash
URL:- http://127.0.0.1/api/user/{id}/roles
Method:- GET
Request Body:- token (required)
Response:- 
6.1) If Success: HTTP_OK response code :- 200 with assigned roles to user in JSON Format.
6.2) If Unsuccess: HTTP_INTERNAL_SERVER_ERROR response code:- 500 Internal Server Error with
error message in JSON Format.
```


7) Assign Role To User

```bash
URL:- http://127.0.0.1/api/user/{id}/role
Method:- POST
Request Body:- token (required), role_id (required,integer)
Response:- 
7.1) If Success: HTTP_OK response code :- 200 with success message in JSON Format.
7.2) If Unsuccess: HTTP_BAD_REQUEST response code :- 400 Bad Request (or) 
HTTP_INTERNAL_SERVER_ERROR
response code:- 500 Internal Server Error with error message in JSON Format.
```


8) Assign Role To User

```bash
URL:- http://127.0.0.1/api/user/{id}/role/{role_id}
Method:- DELETE
Request Body:- token (required)
Response:- 
8.1) If Success: HTTP_OK response code :- 200 with success message in JSON Format.
8.2) If Unsuccess: HTTP_INTERNAL_SERVER_ERROR response code:- 500 Internal Server Error with
error message in JSON Format.
```

## NOTE:- For testing the api you can run the following command


```bash
php artisan test Modules/UserManagement/Tests/Unit/UsersControllerTest.php
```
