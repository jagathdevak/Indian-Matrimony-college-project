VIVAH — Indian Matrimony PHP Demo
================================

XAMPP setup:
1. Extract the folder into C:\xampp\htdocs\
2. Start Apache and MySQL in XAMPP.
3. Open http://localhost/phpmyadmin
4. Import database.sql
5. Open http://localhost/IndianMatrimony/

Demo login:
Email: ananya@example.com
Password: password

The seeded demo profiles use password "password".
You can create new accounts from Create Profile.

Main files:
index.php       Landing page
register.php    Registration
login.php       Login
profiles.php    Search/discover
profile.php     Profile details + send interest
dashboard.php   User dashboard
about.php       How it works
database.sql    MySQL schema + demo profiles

This is a local educational/demo project. For production use, add stronger
validation, CSRF protection, image upload validation, moderation, HTTPS,
rate limiting, privacy controls, and proper verification.


ADMIN
Admin login is available from the footer.
Email: admin@vivah.local
Password: password
Admin dashboard: admin.php

The admin can view member statistics, permanently ban/remove members, and delete member accounts. Banning removes the user row from the users table; related interests are removed automatically by the foreign-key cascade.


PERMANENT EMAIL BAN
-------------------
When an administrator uses "Ban & Remove", the member's email is first stored in
the banned_users table and the member account is then deleted. Related interests
are removed through the existing ON DELETE CASCADE rules. The banned email cannot
be used to create a new account later. A normal "Delete" remains a non-ban deletion.
