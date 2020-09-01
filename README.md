# phpBasicServer
### PHP Server Backend for Testing

The API-Server accesses and modifies entries in our __MySQL__ `employees` database

The `employees` database structure is as defined at;
- https://dev.mysql.com/doc/employee/en/employees-introduction.html  
- https://github.com/datacharmer/test_db


### Setup 

First create a `.env` file at the root of the project with values that follow 
those given in the `.env.example` file.

Then start the API-Server through;
```bash
php -S localhost:8080 -t /public
```

This will start the express server at `localhost:8080`


### Endpoints

- GET /departments  
return all `departments` table entries

- GET /employee/\<EMP_NO\>  
return entries where `employee.emp_no == EMP_NO`  

- GET /employees?gender=\<GENDER\>&hire_date=\<HIRE_DATE\>  
return entries where `employee.hire_date >= HIRE_DATE && employee.gender == GENDER` 

- POST /employees  
create new `employee` entry