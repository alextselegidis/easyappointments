<h1 align="center">
    <br>
    <a href="https://easyappointments.org">
        <img src="https://raw.githubusercontent.com/alextselegidis/easyappointments/master/docs/images/logo.png" alt="Easy!Appointments" width="203">
    </a>
    <br>
    Easy!Appointments
    <br>
</h1>

<h4 align="center">
    A powerful Open Source Web Scheduler, that can be installed on your server. 
</h4>

<p align="center">
  <a href="#about">About</a> •
  <a href="#features">Features</a> •
  <a href="#setup">Setup</a> •
  <a href="#installation">Installation</a> •
  <a href="#license">License</a>
</p>

![banner](docs/images/git-banner.png)

## About

**Easy!Appointments** is a highly customizable web application that allows your customers to book appointments with you 
via the web. Moreover, it provides the ability to sync your data with Google Calendar so you can use them with other 
services. It is an open source project and you can download and install it **even for commercial use**. 
Easy!Appointments will run smoothly with your existing websitSe, because it can be installed in a single folder of the 
server and of course, both sites can share the same database.

## Features

The project was designed to be flexible and reliable so as to be able to meet the needs of any
kind of enterprise. You can read the main features of the system below:

* Full customers and appointments management.
* Services and service providers organization.
* Workflow and booking rules.
* Google Calendar synchronization.
* Email notifications system.
* Standalone installation.
* Translated user interface.
* User community support. 

## Setup

To clone and run this application, you'll need [Git](https://git-scm.com) and [Node.js](https://nodejs.org/en/download/) 
(which comes with [npm](http://npmjs.com)) installed on your computer. From your command line:

```bash
# Clone this repository
$ git clone https://github.com/alextselegidis/easyappointments.git

# Go into the repository
$ cd easyappointments

# Install dependencies
$ npm install
$ composer install

# Run the watcher
$ npm start
```

Note: If you're using Linux Bash for Windows, [see this guide](https://www.howtogeek.com/261575/how-to-run-graphical-linux-desktop-applications-from-windows-10s-bash-shell/) or use `node` from the command prompt.

You can build the files by running the `npm run build` command. This will bundle everything to a `build` directory.

## Installation

Since Easy!Appointments is a web application, it runs on a web server and thus you will need to
perform the following steps in order to install the system on your server:

* Make sure that your server has Apache/Nginx, PHP and MySQL installed.
* Create a new database (or use an existing one).
* Copy the "easyappointments" source folder on your server.
* Make sure that the "storage" directory is writable.
* Rename the "config-sample.php" file to "config.php" and update its contents based on your environment.
* Open the browser on the Easy!Appointments URL and follow the installation guide.

That's it! You can now use Easy!Appointments at your will.

You will find the latest release at [easyappointments.org](https://easyappointments.org).
If you have problems installing or configuring the application visit the
[official support group](https://groups.google.com/forum/#!forum/easy-appointments).
You can also report problems on the [issues page](https://github.com/alextselegidis/easyappointments/issues)
and help the development progress.

## License 

Code Licensed Under [GPL v3.0](https://www.gnu.org/licenses/gpl-3.0.en.html) | Content Under [CC BY 3.0](https://creativecommons.org/licenses/by/3.0/)

---

> Website [alextselegidis.com](https://alextselegidis.com) &nbsp;&middot;&nbsp;
> GitHub [@alextselegidis](https://github.com/alextselegidis) &nbsp;&middot;&nbsp;
> Twitter [@alextselegidis](https://twitter.com/AlexTselegidis)
