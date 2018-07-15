# derivation-nation
Derivation Nation: A Mathproofs Archive Web App
LAMP / Laravel 5 / PHP / MySQL

Overview:

This is the code for a web app I created that I'm no longer hosting. It was called Derivation Nation. The site was a math proofs archive, which allowed anyone to view and search math proofs, and allowed registered users to post, rate for accuracy, and comment on math proofs as well. 


Technical Details:

This web app was built using Laravel 5, a PHP server-side web framework. Laravel's Blade templating engine is used for all the views. MySQL is used as the database, and MathTex is used for displaying mathematical notation. The app was hosted using a LAMP configuration. This was my first time using Laravel, and I absolutely love it. It is extremely intuitive, powerful, and most importantly, very well documented. The official Laracasts (how-to videos) for the framework were also extremely helpful.


Background:

When I would take math courses in college, I found it prudent to first understand the proof behind a math theorem, if I was to correctly apply it to problems within my course work. Knowing WHY a theorem is true helps in knowing HOW to correctly apply it within a given context. The difficulty in achieving this is that most college textbooks don't bother showing you the proof if can't fit on a single page, or maybe because they don't think you care, and expect you to simply take their word for it. Searching online for these proofs is equally fruitless, since many proofs don't have names. One may try to express the theorem as words to plug into the search engine, but the search returns 0 results, since most sites that contain the proof expresses it formulaically, not verbally the way you have, and so your search would not land on the appropriate page. I would end up hopping from one random ancient math website to another, looking through a multitude of poorly organized content, hoping to find what I was looking for. For that reason, I decided to create this app, in which users are required to state the theorem they are proving in words, in addition to the formulaic representation, so that the proofs are more easily found, and would be all in one place on this site.


Key File Locations for Reference:

Routes:
/app/Http/routes.php

Controllers:
/app/Http/Controllers/...

Models (Eloquent ORM):
/app/Services/...

Views (Blade Templating):
/resources/views/...

Global Helper Class:
/app/Helpers/...

Database Table Schemas:
/database/DB_Schema.txt

Public Files (CSS, JS, Images, etc.):
/public

