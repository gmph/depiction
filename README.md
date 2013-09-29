Depiction
=========

## Introduction

Depiction is a simple status sharing service.

It is a web app that allows the user to log in with Twitter and save a status update with a colour. These are written to a database with the user's Twitter username and some metadata. Each user has a profile page which displays their current status and colour at /username.

## Libraries

Depiction uses OAuth authentication and the [Twitter API]('http://dev.twitter.com/api') for managing users. For creating, reading and writing to its SQLite database, it uses [RedBeanPHP]('http://redbeanphp.com/').

## Licence

Depiction is licenced as open-source software under The MIT License.
