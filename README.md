# php-dbcomms-class
The PHP dbcomms class provides a better (in my opinion) way to communicate with the PHP PDO layer.

I wrote this PHP dbcomms class to make recycling code easier across multiple projects. I prefer to not type PDO calls over and over, and prefer to instead use code that I have created that makes the most sense to me. The code within dbcomms.php may not make sense to you, but it does to me, therefore I use it.

One of the biggest things that might prevent you from using this class is how I setup my database names and how this class interacts with them. In order to keep any code from conflicting or any database names from conflicting, I set all of my tables up like this (using a users table for example):

````
users:
  users_id
  users_username
  users_password
````
	
...or if I were creating a "News" table:

````
news:
  news_id
  news_title
  news_text
  news_data
  news_active
````

I precede all of my table column names with the name of the table itself and an underscore. Therefore, when this class interacts with your database, it is going to use the first argument "table" for all of the functions and inject that into the code directly. You can redo all of that in the class file itself if you would like, however to keep all of my code in order and conflicts low, that is how I do things.

Here's how to use it yourself:

## Setup and use

The first step is downloading the dbcomms.php class file. Once you have it downloaded, place it wherever you want, and then require it in your project:

````
require_once("dbcomms.php");
$db = new dbcomms("localhost", "databaseName", "usermame", "password", "options (optional)");
````

You then have six different functions to use:

````
$db->getRow(table, where, operators, params);
$db->getRows(table, where, operators, params, orderBy, ascOrDesc);
$db->insertRow(table, columns, params);
$db->updateRow(table, row, value, where, operators, params);
$db->deleteRow(table, where, operators, params);
$db->countRows(table, column, where, operators, params);
````

The "where", "operators", and "params" fields are comma separated values that are exploded within the class to create an array on the back-end. So if you are wanting to affect multiple columns, such as the id, name, and active column, you need three arguments for each of the where, operators, and params, where they are each in the exact same order as the last:

````
where: id,name,active
operators: =,=,!= (these are comparison operators, where id = 7, name = Ricky, and active != 2)
params: 7,'Ricky',2
````

The other arguments taken in the functions will be explained in each of the separate function sections below.

## getRow(table, where, operators, params)

Here we retrieve a single row from the database. So to select someone from the users table where their ID is equal to 1:

````
$userData = $db->getRow("users","id","=",1);
````

## getRows(table, where, operators, params, orderBy, ascOrDesc)

This argument selects multiple rows at once and returns them as a large array. So if you want to retrieve all users from your database where their ID is greater than 10, and you want to order the results by the username in descending order:

````
$allUsers = $db->getRows("users","id",">",10,"username","DESC");
````

## insertRow(table, columns, params)

Inserting rows is incredibly simple, and if we want to insert someone into the users table then we can do the following:

````
$db->insertRow("users","username,password,dateJoined","$username,$password,$date");
````

## updateRow(table, row, value, where, operators, params)

To update the username of a specific user where his id is equal to 1:

````
$db->updateRow("users","username",$newUsername,"id","=",1);
````

## deleteRow(table, where, operators, params)

If you would like to delete a user's row from the database where their id is equal to 10:

````
$db->deleteRow("users","id","=",10);
````

## countRows(table, column, where, operators, params)

This returns one single integer so that you can count your table for the number of records present. So if you want to count how many users are in your user table where their account active column is 1:

````
$numberOfActiveUsers = $db->countRows("users","active",">",0);
````

## None of this makes sense! There are a lot of gaping holes!

Sorry about that. I have written this class for maximum reusability in my projects. I typically handle very simple database calls, and when I need anything more complex, I insert the code directly int othe project instead of relying on the class.

## You should implement xyz.

Sure, you are free to clone the repo and implement those features yourself if you would like. And if they make sense to me and I can wrap my mind around how it works, I will be more than happy to include it here (with proper credits of course). My main reason for creating this class was so that I could write something that made sense in my brain and that I could type over and over again as quickly as possible.
