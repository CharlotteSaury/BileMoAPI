# BileMoAPI

Project 7 of OpenClassrooms "PHP/Symfony app developper" course.

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/6d5a289025644a1d8d492ffc6deff9fb)](https://www.codacy.com/manual/CharlotteSaury/BileMoAPI?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=CharlotteSaury/BileMoAPI&amp;utm_campaign=Badge_Grade)

# Description

BileMo API is webservice exposing an API.

This project aims to developp a REST API to provide mobile phones for clients catalog.
This first version was developped according to the first client needs:

<ul>
    <li>Consult BileMo products</li> 
    <li>Consult product details</li> 
    <li>Consult customers list related to the client</li> 
    <li>Consult customer details related to the client</li> 
    <li>Add a new customer related to a client</li>
    <li>Delete a customer related to a client</li> 
</ul>

API access is restricted to referenced and authenticated clients.


# Environment : Symfony 5 project
Dependencies (require <a href="https://getcomposer.org/">Composer</a>):
<ul>
    <li><a href="https://github.com/FriendsOfSymfony/FOSRestBundle">FOSRestBundle</a></li>
    <li><a href="https://github.com/schmittjoh/JMSSerializerBundle">JMSSerializerBundle</a></li>
    <li>...</li>
</ul>

# Installation

<p><strong>1 - Git clone the project</strong></p>
<pre>
    <code>https://github.com/CharlotteSaury/BileMoAPI.git</code>
</pre>

<p><strong>2 - Install libraries</strong></p>
<pre>
    <code>php bin/console composer install</code>
</pre>

<p><strong>3 - Create database</strong></p>
<ul>
    <li>a) Update DATABASE_URL .env file with your database configuration.
        <pre>
            <code>DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name</code>
        </pre>
    </li>
    <li>b) Create database: 
        <pre>
            <code>php bin/console doctrine:database:create</code>
        </pre>
    </li>
    <li>c) Create database structure:
        <pre>
            <code>php bin/console make:migration
            php bin/console doctrine:migrations:migrate</code>
            or
            <code>php bin/console doctrine:schema:update --force</code>
        </pre>
    </li>
    <li>d) Insert fictive data
        <pre>
            <code>php bin/console doctrine:fixtures:load</code>
        </pre>
    </li>
</ul>

# Usage

More informations about usage will be available soon !

# Documentation

API documentation will be available soon !





