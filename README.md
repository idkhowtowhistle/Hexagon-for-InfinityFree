# Hexagon PHP & MySQL

This package is a PHP + MySQL version designed for InfinityFree shared hosting.
It retains the main look and feel of Hexagon and includes a basic backend for:

- user registration
- login/logout
- user session
- homepage counter
- initial list of users and games

## How to deploy

1. In the InfinityFree dashboard, create a MySQL database.
2. Edit `includes/config.php` with your InfinityFree MySQL details.
3. Upload the contents of this folder to `htdocs`.
4. Open `yourdomain/install.php` and click `Create tables`.
5. Once you have completed the steps above, delete `install.php` (deleting `database.sql` is optional).

Alternative: instead of `install.php`, you can import `database.sql` in phpMyAdmin.

## Limitations of this version

Standard InfinityFree does not run Node.js, Postgres, rendering processes, RCC/game servers, S3/R2 or persistent workers. Therefore, the advanced features of the original Hexagon require a VPS or other external backend.

This version has been designed to be a web-based solution compatible with InfinityFree.

# Hexagon was originally created by Sushi.

https://hexagon.pw
