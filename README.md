## notebook server
 **notebook server** is an API that allows users to save notes.
 See the live api at [https://jim-notebook-server.herokuapp.com/api](https://jim-notebook-server.herokuapp.com/api)
### What a User can do with the API
- User can sign up and sign in from which he/she will be issued a token
- user can post a note that will be saved to the database as part of user's notes
- User can retrieve his/her notes, paginated
- User can update any of his/her notes.
- User can delete any of his/her notes.
- User can update his profile, including profile picture.
- User can sign out from which the issued token will be expired and blacklisted

### Limitation
- The JWT can expire after a specified period of time. In such situation, you can re-authenticate yourself.
### Technology Stack used
- JWT - for authentication
- Laravel
- Postgres
- Cloudinary - File upload management

### Installation
- Clone this repository
- Change into this project directory `cd notebook-server`
- Add the project environmental variables by creating .env file using the pattern in the env.sample file in this project
- Install the packages `composer install`
- Start the application `php artisan server`
- Navigate to `localhost:8000` on your browser

### Deployment
This guide is for Heroku. For other platforms, composer.json is all yours to tweak.
- push you code to Heroku either using Heroku CLI or Dashboard.
- Set your environmental variables on Heroku which also has the database variables for connecting
to your database.
- Your app should be live. 
- If any error comes up, check your Procfile and make sure you are having the right starting command.

### Note
This project is serving as an API for both the React native (mobile) and the React (web) applications 
I'm currently working on.

### Contribution
- I appreciate. Raise your PR against the master branch.
### Licence
MIT

 
