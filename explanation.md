# Cool Kids Network: User Management System

## Problem to Be Solved
The Cool Kids Network is a website where users can sign up, sign in and interact with other user's data based on their roles. User role can be updated using secure api.

## The challenge was to implement a user management system that allows:
**User Registration:** Anonymous users can sign up using email and rest data like name, country will be autogenrated from randomuser.me api.

**User Login:** Registered users can login using email without password and view the users data on the basis of role.

**Role-Based Access:** Users with special roles ("Cooler Kid" and "Coolest Kid") can view other user's data with varying levels of access.

**Role Management:** Admins can change user roles through a secure API endpoint.

The goal was to create a customised solution for user management system with custom roles, used based data access with the scutiry, scalability and easy to use.

## Technical Specification
The solution is built on WordPress and custom code with the help of creating a custom plugin. Here is how it works

### User Registration: Here is how the registration works
**Sign-Up Form:** Sign up form created using shortcode "[ckn_signup_form]". It will render the sign up form and when a user click on sign up button it will register a user with ajax.

**Character Generation:** On registration, a fake identity (first name, last name, country) is generated using the randomuser.me API and stored as user meta.

**Default Role:** New users are assigned the "Cool Kid" role by default.   

### User Login : Here is how it work
**Login Form:** Login form created using the shortcode "[ckn_login_form]". User can login using the email without password. Once the user is login, it will redirect it to character data page.

### Role-Based Access: View character data on the basis of login user's role.

**Custom Roles:** Three roles are created for character:

*Cool Kid:* Can view their own character data.

*Cooler Kid:* Can view other character's data but not their email and role.

*Coolest Kid:* Can view other character's data names, countries, emails, and roles.

### Role Management API : Rest API endpoint to update user role
**Protected Endpoint:** A custom REST API endpoint (/wp-json/coolkids/v1/change-role) allows admins to change user roles.

**Authentication:** The endpoint uses nonce for secure access.

**Input Validation:** The endpoint validates inputs (email, first name, last name, role) and ensures only valid roles are assigned.

## Technical Decisions
**WordPress as the Backend:**  WordPress was chosen for its flexibility, built-in user management, and REST API capabilities. It allows rapid development and easy integration with third-party tools.

**Custom User Roles:** Custom roles ("Cool Kid," "Cooler Kid," "Coolest Kid") were created to enforce role-based access control. This ensures users only see data they are authorized to access.

**Random User API:** The randomuser.me API was used to generate fake identities for new users.

**Input Validation and Sanitization:** All inputs are validated and sanitized to prevent security vulnerabilities. This ensures the API is secure and robust.

## How the Solution Achieves the Desired Outcome
The solution meets the requirements outlined in the given task:

**User Story 1: Anonymous User Sign-Up**
Users can register using their email address.
A character is automatically generated and stored as user meta.
The default role "Cool Kid" is assigned.

**User Story 2: Logged-In User Views Character Data**
User can login with email without password and Logged-in users can view their character data (first name, last name, country, email, role).

This is achieved using the get_user_meta function.

**User Story 3: Cooler Kid Access**
Users with the "Cooler Kid" role can view other users' names and countries.
This is enforced using role-based access control.

**User Story 4: Coolest Kid Access**
Users with the "Coolest Kid" role can view other users' names, countries, emails, and roles.

This is enforced using role-based access control.

**User Story 5: Admin Role Management**
Admins can change user roles through a secure API endpoint. The endpoint is protected by nonce and validates all inputs

## Future Improvements
**Password Authentication:** Add password validation for secure login.

**UI Enhancements:** Improve the user interface for better usability.

**Logging and Monitoring**: Add logging and monitoring for API activity.

## How I Approach a Problem
**Understand the Requirements:** I start by thoroughly understanding the problem and the desired outcomes. For this project, I carefully analyzed the user stories and identified the key features needed:
> User registration and character generation.
> Role-based access control.
> A secure API for role management.

**Break Down the Problem:** I break the problem into smaller, manageable tasks. For example:
> How to handle user registration?
> How to generate and store character data?
> How to implement role-based access control?
> How to secure the API endpoint?

**Research and Explore Solutions:** 
*Login:* I was exploring to login using wordpress login form but it require username and password but in our case we need to login using only the email so I decided to build custom login form and manage the login process.
*Roles:* To create roles in wordpress we can use other plugin which help us to create roles but I don't want to user other plugins for that so I created it by code.

## How I Think About It
I always keep the end user in mind. For this project, I focused on creating a seamless experience for:
Anonymous users signing up and generating a character.
Logged-in users viewing their data.
Admins managing user roles through an API.

## Why I Chose This Direction
Wordpress as the backend : It is widely used platform with user management, security and Rest Api capabilities. It allow rapid development and third party integration easily. Custom roles are easy to manage in wordpress. This is easy to manage and scale in future.

